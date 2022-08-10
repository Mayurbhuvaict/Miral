<?php declare(strict_types=1);

namespace ICTECHLimitedLoginAttempts\Core\Checkout\Customer\SalesChannel;


use Exception;
use ICTECHLimitedLoginAttempts\Core\Checkout\Customer\Exception\DayLockoutException;
use ICTECHLimitedLoginAttempts\Core\Checkout\Customer\Exception\LockoutException;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Event\CustomerBeforeLoginEvent;
use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Checkout\Customer\Exception\BadCredentialsException;
use Shopware\Core\Checkout\Customer\Exception\CustomerAuthThrottledException;
use Shopware\Core\Checkout\Customer\Exception\CustomerNotFoundException;
use Shopware\Core\Checkout\Customer\Exception\InactiveCustomerException;
use Shopware\Core\Checkout\Customer\Password\LegacyPasswordVerifier;
use Shopware\Core\Checkout\Customer\SalesChannel\LoginRoute;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\RateLimiter\Exception\RateLimitExceededException;
use Shopware\Core\Framework\RateLimiter\RateLimiter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\Context\CartRestorer;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\ContextTokenResponse;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LoginRouteDecorated extends LoginRoute
{
    private EventDispatcherInterface $eventDispatcher;

    private EntityRepositoryInterface $customerRepository;

    private LegacyPasswordVerifier $legacyPasswordVerifier;

    private CartRestorer $restorer;

    private RequestStack $requestStack;

    private RateLimiter $rateLimiter;

    const ADMIN_DATETIME_FORMAT = 'H:i';

    protected EntityRepositoryInterface $ictechLimitedLoginAttemptsRepository;

    protected SystemConfigService $systemConfigService;


    public function __construct(
        EventDispatcherInterface  $eventDispatcher,
        EntityRepositoryInterface $customerRepository,
        LegacyPasswordVerifier    $legacyPasswordVerifier,
        CartRestorer              $restorer,
        RequestStack              $requestStack,
        RateLimiter               $rateLimiter,
        //extra added
        EntityRepositoryInterface $ictechLimitedLoginAttemptsRepository,
        SystemConfigService       $systemConfigService

    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->customerRepository = $customerRepository;
        $this->legacyPasswordVerifier = $legacyPasswordVerifier;
        $this->restorer = $restorer;
        $this->requestStack = $requestStack;
        $this->rateLimiter = $rateLimiter;
        //extra added
        $this->ictechLimitedLoginAttemptsRepository = $ictechLimitedLoginAttemptsRepository;
        $this->systemConfigService = $systemConfigService;

    }

    public function login(RequestDataBag $data, SalesChannelContext $context): ContextTokenResponse
    {
        $email = $data->get('email', $data->get('username'));

        if (empty($email) || empty($data->get('password'))) {
            throw new BadCredentialsException();
        }

        $event = new CustomerBeforeLoginEvent($context, $email);
        $this->eventDispatcher->dispatch($event);

        if ($this->requestStack->getMainRequest() !== null) {
            $cacheKey = strtolower($email) . '-' . $this->requestStack->getMainRequest()->getClientIp();

            try {
                $this->rateLimiter->ensureAccepted(RateLimiter::LOGIN_ROUTE, $cacheKey);
            } catch (RateLimitExceededException $exception) {
                throw new CustomerAuthThrottledException($exception->getWaitTime(), $exception);
            }
        }

        try {
            $customer = $this->getCustomerByLogin(
                $email,
                $data->get('password'),
                $context
            );
        } catch (CustomerNotFoundException|LockoutException|DayLockoutException|BadCredentialsException $exception) {
            $limitedLoginMinutesLockout = $this->systemConfigService->get('ICTECHLimitedLoginAttempts.config.limitedLoginMinutesLockout');
            $limitedLoginMinutesLockoutDay = "24";
            if($exception instanceof DayLockoutException){
                throw new DayLockoutException((int)$limitedLoginMinutesLockoutDay);
            }
            if($exception instanceof LockoutException){
                throw new LockoutException((int)$limitedLoginMinutesLockout);
            }
            throw new UnauthorizedHttpException('json', $exception->getMessage());
        }


        if (isset($cacheKey)) {
            $this->rateLimiter->reset(RateLimiter::LOGIN_ROUTE, $cacheKey);
        }

        if (!$customer->getActive()) {
            throw new InactiveCustomerException($customer->getId());
        }

        $context = $this->restorer->restore($customer->getId(), $context);
        $newToken = $context->getToken();

        $this->customerRepository->update([
            [
                'id' => $customer->getId(),
                'lastLogin' => new \DateTimeImmutable(),
            ],
        ], $context->getContext());

        $event = new CustomerLoginEvent($context, $customer, $newToken);
        $this->eventDispatcher->dispatch($event);

        return new ContextTokenResponse($newToken);
    }

    protected function getCustomerByLogin(string $email, string $password, SalesChannelContext $context): CustomerEntity
    {

        $customer = $this->getCustomerByEmail($email, $context);

        if ($customer->hasLegacyPassword()) {
            if (!$this->legacyPasswordVerifier->verify($password, $customer)) {
                throw new BadCredentialsException();
            }
            $this->updatePasswordHash($password, $customer, $context->getContext());
            return $customer;
        }
        $activeLimitedLogin = $this->systemConfigService->get('ICTECHLimitedLoginAttempts.config.loadLimitedLogin');
        if (!password_verify($password, $customer->getPassword() ?? '')) {
            if($activeLimitedLogin == true){
                $this->limitedLoginAttempts($customer, $context->getContext());
            }
            throw new BadCredentialsException();
        }
        return $customer;
    }

    private function getCustomerByEmail(string $email, SalesChannelContext $context): CustomerEntity
    {
        $criteria = new Criteria();
        $criteria->setTitle('login-route');
        $criteria->addFilter(new EqualsFilter('customer.email', $email));

        $result = $this->customerRepository->search($criteria, $context->getContext());

        $result = $result->filter(static function (CustomerEntity $customer) use ($context) {
            // Skip guest users
            if ($customer->getGuest()) {
                return null;
            }

            // If not bound, we still need to consider it
            if ($customer->getBoundSalesChannelId() === null) {
                return true;
            }

            // It is bound, but not to the current one. Skip it
            if ($customer->getBoundSalesChannelId() !== $context->getSalesChannel()->getId()) {
                return null;
            }

            return true;
        });

        if ($result->count() !== 1) {
            throw new BadCredentialsException();
        }

        return $result->first();
    }

    private function updatePasswordHash(string $password, CustomerEntity $customer, Context $context): void
    {
        $this->customerRepository->update([
            [
                'id' => $customer->getId(),
                'password' => $password,
                'legacyPassword' => null,
                'legacyEncoder' => null,
            ],
        ], $context);
    }

    private function limitedLoginAttempts(CustomerEntity $customer, Context $context): void
    {

        $limitedLoginAllowedRetries = $this->systemConfigService->get('ICTECHLimitedLoginAttempts.config.limitedLoginAllowedRetries');
        $limitedLoginMinutesLockout = $this->systemConfigService->get('ICTECHLimitedLoginAttempts.config.limitedLoginMinutesLockout');
        $limitedLoginLockoutIncreasesTime = $this->systemConfigService->get('ICTECHLimitedLoginAttempts.config.limitedLoginLockoutIncreasesTime');
        $limitedLoginHoursUntilRetriesAreReset = $this->systemConfigService->get('ICTECHLimitedLoginAttempts.config.limitedLoginHoursUntilRetriesAreReset');

        //set criteria
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customerId', $customer->getId()));

        $existingCustomerId = $this->ictechLimitedLoginAttemptsRepository->search($criteria, $context)->first();
        if($existingCustomerId !== null)
        {
            //dd("main if");
            $loginAttempt = $existingCustomerId->getAttempt();
            $loginLockout = $existingCustomerId->getAttemptLockout();
            $mainId = $existingCustomerId->getId();
            if($existingCustomerId->getUpdatedAt() == null){
                $loginUpdatedAt = $existingCustomerId->getCreatedAt()->format(self::ADMIN_DATETIME_FORMAT);
            }
            else{
                $loginUpdatedAt = $existingCustomerId->getUpdatedAt()->format(self::ADMIN_DATETIME_FORMAT);
            }
            $today = date('H:i');
            $end = strtotime($today);
            $start = strtotime($loginUpdatedAt);
            $minutes = ($end - $start) / 60;

            if ($loginLockout > $limitedLoginLockoutIncreasesTime) {
                //24 hour = 1440 min(24 * 60 min)
                //in bellow line replace 3 by 1440
                //$oneDayMinutes = 1440;
                $oneDayHours = "24";
                $oneDayMinutes = "3";
                //check 1440 minutes but pass 24 in DayLockoutException bcz we have custom msg for hours
                // if (3 > $locuoutMins) {
                if ($oneDayHours > $minutes) {
                   // throw new Exception("(3 minutes)pls try after 24 hours");
                    throw new DayLockoutException((int)$oneDayHours);
                } else
                {
                    $deleteData[] = [
                        'id' => $mainId,
                    ];
                    $this->ictechLimitedLoginAttemptsRepository->delete($deleteData, $context);
                    $upsertData[] = [
                        'id' => Uuid::randomHex(),
                        'customerId' => $customer->getId(),
                        'attempt' => 1,
                        'attemptLockout' => 1,
                       // 'attemptLockout' => 0,
                    ];
//                        $upsertData[] = [
//                            'id' => $existingCustomerId->getId(),
//                            'customerId' => $existingCustomerId->getcustomerId(),
//                            'attempt' => 1,
//                            'attemptLockout' => 0,
//                            'updated_at' => null
//                        ];
                }
            }
            else
            {
               // dd("not lockout");
                if ($loginAttempt >= $limitedLoginAllowedRetries)
                {
                    //dd("attmpt is bigger");
                    // $today = date('H:i');
                    // $end = strtotime($today);
                    // $start = strtotime($loginUpdatedAt);
                    // $minutes = ($end - $start) / 60;
                    if ($limitedLoginMinutesLockout > $minutes) {
                        throw new LockoutException((int)$limitedLoginMinutesLockout);
                    }
                    else{
                        $upsertData[] = [
                            'id' => $existingCustomerId->getId(),
                            'customerId' => $existingCustomerId->getcustomerId(),
                            'attempt' => 1,
                            'attemptLockout' => $existingCustomerId->getAttemptLockout() + 1,
                        ];
                    }

                }
                else
                {
                    //1*24*60*60
                    // $today = date('H:i');
                    // $end = strtotime($today);
                    // $start = strtotime($loginUpdatedAt);
                    // //$minutes = ($end - $start) / 60;
                    // $minutesRetry = ($end - $start) / 60;
                    //$freshAttemptTime = $limitedLoginHoursUntilRetriesAreReset * 60 ;
                    //dd($freshAttemptTime);
                    //$freshAttemptTime > $minutesRetry
                    if($loginAttempt < $limitedLoginAllowedRetries &&  20 < $minutes)
                    {
                        $deleteData[] = [
                            'id' => $mainId,
                        ];
                        $this->ictechLimitedLoginAttemptsRepository->delete($deleteData,  $context);
                        $upsertData[] = [
                            'id' => Uuid::randomHex(),
                            'customerId' => $customer->getId(),
                            'attempt' => 1,
                            'attemptLockout' => 1,
                            //'attemptLockout' => 0,
                        ];
//                    $upsertData[] = [
//                        'id' => $existingCustomerId->getId(),
//                        'customerId' => $existingCustomerId->getcustomerId(),
//                        'attempt' => 1,
//                        'attemptLockout' => 0,
//                        //'updated_at' => null,
//                    ];
                    }
                    else{
                        $upsertData[] = [
                            "id" => $existingCustomerId->getId(),
                            'customerId' => $existingCustomerId->getCustomerId(),
                            'attempt' => $existingCustomerId->getAttempt() + 1,
                            //'attemptLockout' => 0,
                        ];
                    }
                }
            }
        }
        else
        {
            $upsertData[] = [
                'id' => Uuid::randomHex(),
                'customerId' => $customer->getId(),
                'attempt' => 1,
                'attemptLockout' => 1,
                //'attemptLockout' => 0,
            ];
        }
        $this->ictechLimitedLoginAttemptsRepository->upsert($upsertData, $context);

    }
}
