<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;

class SettingsController extends AppController
{
    public function adminIndex(): void
    {
        if ($this->request->is(['post', 'put'])) {
            if (Configure::read('demo_mode')) {
                $this->Flash->error(__('デモモードでは保存できません'));
                return;
            }

            // フォームから送られたデータを取得
            // フラットな構造で来る（['title' => ..., 'copyright' => ...]）
            $postData = $this->request->getData();

            // 除外するキー（Formヘルパーが自動で付けるフィールド）
            $excludeKeys = ['_Token', '_csrfToken'];
            $settingData = [];
            foreach ($postData as $key => $value) {
                if (!in_array($key, $excludeKeys, true) && $value !== null) {
                    $settingData[$key] = (string)$value;
                }
            }

            if (!empty($settingData)) {
                $this->fetchTable('Settings')->setSettings($settingData);

                $session = $this->request->getSession();
                foreach ($settingData as $key => $value) {
                    $session->write('Setting.' . $key, $value);
                }
                $this->Flash->success(__('設定を保存しました'));
            } else {
                $this->Flash->error(__('保存するデータがありません'));
            }
        }

        $settings = $this->fetchTable('Settings')->getSettings();
        $colors   = Configure::read('theme_colors');
        $this->set(compact('settings', 'colors'));
    }
}