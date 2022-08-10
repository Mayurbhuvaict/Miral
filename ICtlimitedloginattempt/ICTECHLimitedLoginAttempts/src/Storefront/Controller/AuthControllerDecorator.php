<?php declare(strict_types=1);

namespace ICTECHLimitedLoginAttempts\Storefront\Controller;

use ICTECHLimitedLoginAttempts\Core\Checkout\Customer\Exception\LockoutException;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Checkout\Customer\Exception\BadCredentialsException;
use Shopware\Core\Checkout\Customer\Exception\CustomerAuthThrottledException;
use Shopware\Core\Checkout\Customer\Exception\InactiveCustomerException;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractLoginRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractLogoutRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractResetPasswordRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractSendPasswordRecoveryMailRoute;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\AuthController;
use Shopware\Storefront\Page\Account\Login\AccountLoginPageLoadedHook;
use Shopware\Storefront\Page\Account\Login\AccountLoginPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


/**
 * @RouteScope(scopes={"storefront"})
 */
class AuthControllerDecorator extends AuthController
{


    public function __construct(
        AccountLoginPageLoader                $loginPageLoader,
        EntityRepositoryInterface             $customerRecoveryRepository,
        AbstractSendPasswordRecoveryMailRoute $sendPasswordRecoveryMailRoute,
        AbstractResetPasswordRoute            $resetPasswordRoute,
        AbstractLoginRoute                    $loginRoute,
        AbstractLogoutRoute                   $logoutRoute,
        CartService                           $cartService
    )
    {

        parent::__construct($this->loginPageLoader = $loginPageLoader, $customerRecoveryRepository, $sendPasswordRecoveryMailRoute,
            $resetPasswordRoute, $this->loginRoute = $loginRoute, $logoutRoute, $this->cartService = $cartService);

    }

    public function loginPage(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        /** @var string $redirect */
        $redirect = $request->get('redirectTo', 'frontend.account.home.page');

        $customer = $context->getCustomer();

        if ($customer !== null && $customer->getGuest() === false) {
            $request->request->set('redirectTo', $redirect);

            return $this->createActionResponse($request);
        }

        $page = $this->loginPageLoader->load($request, $context);

        $this->hook(new AccountLoginPageLoadedHook($page, $context));

        return $this->renderStorefront('@Storefront/storefront/page/account/register/index.html.twig', [
            'redirectTo' => $redirect,
            'redirectParameters' => $request->get('redirectParameters', json_encode([])),
            'page' => $page,
            'loginError' => (bool) $request->get('loginError'),
            'waitTime' => $request->get('waitTime'),
            'errorSnippet' => $request->get('errorSnippet'),
            'data' => $data,
            //'lockoutwaitingTime' => $request->get('waitingTime'),

        ]);
    }

    public function login(Request $request, RequestDataBag $data, SalesChannelContext $context): Response
    {
        $customer = $context->getCustomer();


        if ($customer !== null && $customer->getGuest() === false) {
            return $this->createActionResponse($request);
        }

        try {
            $token = $this->loginRoute->login($data, $context)->getToken();
            if (!empty($token)) {
                $this->addCartErrors($this->cartService->getCart($token, $context));
                return $this->createActionResponse($request);
            }

        } catch (LockoutException|DayLockoutException|BadCredentialsException|UnauthorizedHttpException|InactiveCustomerException|CustomerAuthThrottledException $e) {
            //dd("error");
            //dd($e);
            if ($e instanceof InactiveCustomerException) {
                $errorSnippet = $e->getSnippetKey();
            }


            if ($e instanceof CustomerAuthThrottledException) {
                $waitTime = $e->getWaitTime();
            }

            if ($e instanceof LockoutException) {
                 $waitingTime = $e->getWaitingTime();
            }

            if ($e instanceof DayLockoutException) {
                $waitingTime = $e->getWaitingTime();
            }

        }
        $data->set('password', null);

        return $this->forwardToRoute(
            'frontend.account.login.page',
            [
                'loginError' => true,
                'errorSnippet' => $errorSnippet ?? null,
                'waitTime' => $waitTime ?? null,
                'lockoutwaitingTime' => $waitingTime ?? null,
            ]
        );
    }

}
