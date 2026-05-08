<?php
$ctrl   = $this->request->getParam('controller');
$action = $this->request->getParam('action');
?>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
            <?php
            $is_active = ($ctrl === 'Users' && $action !== 'adminSetting') ? ' active' : '';
            echo '<li class="' . $is_active . '">' . $this->Html->link(__('ユーザ'), ['controller' => 'Users', 'action' => 'adminIndex']) . '</li>';

            $is_active = ($ctrl === 'Groups') ? ' active' : '';
            echo '<li class="' . $is_active . '">' . $this->Html->link(__('グループ'), ['controller' => 'Groups', 'action' => 'adminIndex']) . '</li>';

            $is_active = in_array($ctrl, ['Courses', 'Contents', 'ContentsQuestions', 'EnquetesQuestions']) ? ' active' : '';
            echo '<li class="' . $is_active . '">' . $this->Html->link(__('コース'), ['controller' => 'Courses', 'action' => 'adminIndex']) . '</li>';

            $is_active = ($ctrl === 'Infos') ? ' active' : '';
            echo '<li class="' . $is_active . '">' . $this->Html->link(__('お知らせ'), ['controller' => 'Infos', 'action' => 'adminIndex']) . '</li>';

            $is_active = ($ctrl === 'Records') ? ' active' : '';
            echo '<li class="' . $is_active . '">' . $this->Html->link(__('学習履歴'), ['controller' => 'Records', 'action' => 'adminIndex']) . '</li>';

            if (!empty($loginedUser) && ($loginedUser['role'] ?? '') === 'admin'):
                $is_active = ($ctrl === 'Settings') ? ' active' : '';
                echo '<li class="' . $is_active . '">' . $this->Html->link(__('システム設定'), ['controller' => 'Settings', 'action' => 'adminIndex']) . '</li>';
            endif;
            ?>
        </ul>
        </div>
    </div>
</nav>
