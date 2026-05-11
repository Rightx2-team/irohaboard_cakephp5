<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;

class LanguageController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['switch']);
    }

    public function switch(string $lang): Response
    {
        $allowed = ['ja_JP', 'en_US'];
        if (!in_array($lang, $allowed, true)) {
            $lang = 'ja_JP';
        }
        $this->request->getSession()->write('Config.language', $lang);

        // If a return parameter is given, redirect there; otherwise go home / return パラメータがあればそのページへ、なければホームへ
        $returnUrl = $this->request->getQuery('return');
        if ($returnUrl && str_starts_with($returnUrl, '/') && !str_starts_with($returnUrl, '//')) {
            return $this->redirect($returnUrl);
        }
        return $this->redirect('/');
    }
}
