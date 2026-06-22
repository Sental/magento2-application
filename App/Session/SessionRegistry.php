<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Application\App\Session;

use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use WeakMap;

class SessionRegistry implements ResetAfterRequestInterface
{
    /** @var WeakMap<SessionManagerInterface, bool> */
    private WeakMap $sessions;

    public function __construct()
    {
        $this->sessions = new WeakMap();
    }

    public function add(SessionManagerInterface $sessionManager): void
    {
        $this->sessions[$sessionManager] = true;
    }

    public function startSessions(): void
    {
        foreach ($this->sessions as $session => $state) {
            $session?->start();
        }
    }

    public function closeSessions(): void
    {
        foreach ($this->sessions as $session => $state) {
            $session?->writeClose();
        }
    }

    /**
     * Drop the previous request's sessions so startSessions() never re-starts sessions from another
     * request or area on a persistent worker. Sessions are lazily re-registered on first access.
     */
    public function _resetState(): void
    {
        $this->sessions = new WeakMap();
    }
}
