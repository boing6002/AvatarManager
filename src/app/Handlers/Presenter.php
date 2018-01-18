<?php

namespace LaravelEnso\AvatarManager\app\Handlers;

use LaravelEnso\AvatarManager\app\Models\Avatar;

class Presenter extends Handler
{
    private const DefaultAvatar = 'profile.png';

    private $image;

    public function __construct($id)
    {
        parent::__construct();

        $avatar = Avatar::find($id);

        $this->image = $avatar
            ? $avatar->saved_name
            : self::DefaultAvatar;
    }

    public function show()
    {
        return $this->fileManager->getInline($this->image);
    }
}
