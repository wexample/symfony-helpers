<?php

namespace Wexample\SymfonyHelpers\Helper;

abstract class NotifierHelper
{
    final public const CHANNEL_EMAIL = VariableHelper::EMAIL;

    final public const CHANNEL_CHAT_ROCKETCHAT = 'chat/rocketchat';
}
