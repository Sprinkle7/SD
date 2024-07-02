<?php


namespace App\Helper\SystemMessage;


use Illuminate\Filesystem\Cache;

class SystemMessage
{
    protected $entity = '';
    protected $messages = [];

    public function __construct($language, $entity, $defaultLanguage)
    {
        $translation = [];
        $appPath = app_path() . '/Helper/SystemMessage/';
        if (file_exists($appPath . 'Entities/Lang/' . $language . '/Entities.php'))
            $translation = require $appPath . 'Entities/Lang/' . $language . '/Entities.php';
        else
            $translation = require $appPath . 'Entities/Lang/' . $defaultLanguage . '/Entities.php';
        $this->entity = $translation[$entity] . ' ';

        if (file_exists($appPath . 'Messages/Lang/' . $language . '/Messages.php'))
            $translation = require $appPath . 'Messages/Lang/' . $language . '/Messages.php';
        else
            $translation = require $appPath . 'Messages/Lang/' . $defaultLanguage . '/Messages.php';
        $this->messages = $translation;
        unset($translation);
    }


    public function create()
    {
        return $this->entity . $this->messages['create'];
    }

    public function update()
    {
        return $this->entity . $this->messages['update'];
    }
    
    public function duplicate()
    {
        return $this->entity . $this->messages['duplicate'];
    }

    public function search()
    {
        return $this->entity . $this->messages['search'];
    }

    public function fetch()
    {
        return $this->entity . $this->messages['fetch'];
    }

    public function delete()
    {
        return $this->entity . $this->messages['delete'];
    }

    public function activate()
    {
        return $this->entity . $this->messages['activate'];
    }

    public function deactivate()
    {
        return $this->entity . $this->messages['deactivate'];
    }

    public function setDefault()
    {
        return $this->entity . $this->messages['default'];
    }

    public function isExist()
    {
        return $this->entity . $this->messages['isExist'];
    }

    public function isDefault()
    {
        return $this->messages['isDefault'];
    }

    public function uploadFile()
    {
        return $this->messages['uploadFile'];
    }

    public function deleteFile()
    {
        return $this->messages['deleteFile'];
    }


    public function addTranslation()
    {
        return $this->entity . $this->messages['addTranslation'];
    }

    public function duplicateTranslation()
    {
        return $this->messages['duplicateTranslation'];
    }

    public function wrongCredentials()
    {
        return $this->messages['wrongCredentials'];
    }

    public function unableToDelete()
    {
        return $this->entity . $this->messages['unableToDelete'];

    }

    public function unableToUpdateCombs()
    {
        return $this->entity . $this->messages['unableToUpdateCombs'];

    }


    public function productAttached()
    {
        return $this->messages['productAttached'];
    }

    public function error404()
    {
        return $this->messages['404'];
//        return $this->entity . $this->messages['404'];
    }

    public function error401()
    {
        return $this->messages['401'];
    }

    public function error500()
    {
        return $this->messages['500'];
    }

}
