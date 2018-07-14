<?php

namespace LaravelEnso\AvatarManager\app\Classes;

use LaravelEnso\Core\app\Models\User;
use LaravelEnso\AvatarManager\app\Models\Avatar;
use LaravelEnso\ImageTransformer\app\Classes\ImageTransformer;

class Storer extends Handler
{
    const ImageHeight = 250;
    const ImageWidth = 250;

    private $user;
    private $avatar;

    public function __construct(User $user, array $avatar)
    {
        parent::__construct();

        $this->fileManager->tempPath(
            config('enso.config.paths.temp')
        );

        $this->user = $user;
        $this->avatar = $avatar;
    }

    public function run()
    {
        $avatar = null;

        try {
            \DB::transaction(function () use (&$avatar) {
                $this->user->avatar->delete();
                $this->processImage();
                $this->fileManager->startUpload($this->avatar);
                $avatar = $this->store();
                $this->fileManager->commitUpload();
            });
        } catch (\Exception $exception) {
            $this->fileManager->deleteTempFiles();
            throw $exception;
        }

        return $avatar;
    }

    private function store()
    {
        return Avatar::create(
            $this->fileManager->uploadedFiles()->first() +
            ['user_id' => $this->user->id]
        );
    }

    private function processImage()
    {
        (new ImageTransformer(collect($this->avatar)->first()))
            ->resize(self::ImageWidth, self::ImageHeight)
            ->optimize();
    }
}
