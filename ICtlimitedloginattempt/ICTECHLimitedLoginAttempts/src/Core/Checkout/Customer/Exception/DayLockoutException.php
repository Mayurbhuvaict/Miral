<?php
declare(strict_types=1);


namespace ICTECHLimitedLoginAttempts\Core\Checkout\Customer\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class DayLockoutException extends ShopwareHttpException
{
    private int $waitingTime;

    public function __construct(int $waitingTime, ?\Throwable $e = null)
    {
        $this->waitingTime = $waitingTime;

        parent::__construct(
            'Account has been LockedOut for 24 -- {{ seconds }} hours.',
            ['seconds' => $this->waitingTime],
            $e
        );
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__CUSTOMER_AUTH_THROTTLED';

    }

    public function getStatusCode(): int
    {
        return Response::HTTP_TOO_MANY_REQUESTS;
    }

    public function getWaitingTime(): int
    {
        return $this->waitingTime;
    }
    public function getSnippetKey(): string
    {
        return 'wait for some time';
       // return 'wait for some time {{ $this->waitingTime }}';
    }
}
