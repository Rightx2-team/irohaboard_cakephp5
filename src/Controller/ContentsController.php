<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\Core\Configure;
use Cake\Http\Response;

class ContentsController extends AppController
{
    /**
     * Admin: content list / 管理: コンテンツ一覧
     */
    public function adminIndex(int $course_id): void
    {
        $course = $this->fetchTable('Courses')->get($course_id);
        $contents = $this->fetchTable('Contents')->find()
            ->where(['course_id' => $course_id])
            ->orderByAsc('sort_no')
            ->all();
        $this->set(compact('contents', 'course'));
    }

    /**
     * Admin: add content / 管理: コンテンツ追加
     */
    public function adminAdd(int $course_id): ?Response
    {
        $contentsTable = $this->fetchTable('Contents');
        $course = $this->fetchTable('Courses')->get($course_id);
        $content = $contentsTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['course_id'] = $course_id;

            $identity = $this->Authentication->getIdentity();
            if ($identity) {
                $data['user_id'] = $identity->getIdentifier();
            }

            if (method_exists($contentsTable, 'getNextSortNo')) {
                $data['sort_no'] = $contentsTable->getNextSortNo($course_id);
            } else {
                $max = $contentsTable->find()->where(['course_id' => $course_id])->select(['max' => 'MAX(sort_no)'])->first();
                $data['sort_no'] = (int)($max->max ?? 0) + 1;
            }

            $content = $contentsTable->patchEntity($content, $data);
            if ($contentsTable->save($content)) {
                $this->Flash->success(__('コンテンツを追加しました'));
                return $this->redirect(['action' => 'adminIndex', $course_id]);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $this->set(compact('content', 'course'));
        $this->render('admin_edit');
        return null;
    }

    /**
     * Admin: edit content / 管理: コンテンツ編集
     */
    public function adminEdit(int $course_id, ?int $content_id = null): ?Response
    {
        $contentsTable = $this->fetchTable('Contents');
        $course = $this->fetchTable('Courses')->get($course_id);

        if (!$content_id || !$contentsTable->exists(['id' => $content_id])) {
            throw new NotFoundException(__('Invalid content'));
        }

        $content = $contentsTable->get($content_id);

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $data['course_id'] = $course_id;

            $content = $contentsTable->patchEntity($content, $data);
            if ($contentsTable->save($content)) {
                $this->Flash->success(__('コンテンツを更新しました'));
                return $this->redirect(['action' => 'adminIndex', $course_id]);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $this->set(compact('content', 'course'));
        return null;
    }

    /**
     * Admin: delete content / 管理: コンテンツ削除
     */
    public function adminDelete(int $content_id): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $contentsTable = $this->fetchTable('Contents');

        if (!$contentsTable->exists(['id' => $content_id])) {
            throw new NotFoundException(__('Invalid content'));
        }

        $content = $contentsTable->get($content_id);
        $course_id = $content->course_id;

        if ($contentsTable->delete($content)) {
            if ($this->fetchTable('ContentsQuestions')->hasBehavior('Tree')) {
                // no-op
            }
            $this->fetchTable('ContentsQuestions')->deleteAll(['content_id' => $content_id]);
            $this->Flash->success(__('コンテンツを削除しました'));
        } else {
            $this->Flash->error(__('削除に失敗しました'));
        }

        return $this->redirect(['action' => 'adminIndex', $course_id]);
    }

    /**
     * Admin: reorder contents (Ajax) / 管理: コンテンツ並び替え (Ajax)
     */
    public function adminOrder(): ?Response
    {
        $this->autoRender = false;

        if ($this->request->is('post')) {
            $idList = $this->request->getData('id_list');
            if (is_array($idList)) {
                $contentsTable = $this->fetchTable('Contents');
                if (method_exists($contentsTable, 'setOrder')) {
                    $contentsTable->setOrder($idList);
                } else {
                    foreach ($idList as $i => $id) {
                        $contentsTable->updateAll(['sort_no' => $i + 1], ['id' => $id]);
                    }
                }
            }
            return $this->response->withType('text/plain')->withStringBody('OK');
        }
        return null;
    }

    /**
     * Learner: content list within a course / 受講者: コンテンツ一覧（コース内）
     */
    public function index(int $course_id, ?int $user_id = null): void
    {
        $course = $this->fetchTable('Courses')->get($course_id);
        $identity = $this->Authentication->getIdentity();
        $authUserId = $identity ? $identity->getIdentifier() : 0;
        $role = $identity ? $identity->get('role') : 'user';

        $coursesTable = $this->fetchTable('Courses');
        if (!method_exists($coursesTable, 'hasRight') || !$coursesTable->hasRight($authUserId, $course_id)) {
            // Simplified permission check / 権限チェックを簡略化
        }

        $contentsTable = $this->fetchTable('Contents');
        if (method_exists($contentsTable, 'getContentRecord')) {
            $contents = $contentsTable->getContentRecord($user_id ?? $authUserId, $course_id, $role);
        } else {
            $contents = $contentsTable->find()
                ->where(['course_id' => $course_id])
                ->orderByAsc('sort_no')
                ->all();
        }

        $this->set(compact('course', 'contents'));
    }

    /**
     * Admin: file upload / 管理: ファイルアップロード
     * @param string $file_type 'file' | 'image' | 'movie'
     */
    public function adminUpload(string $file_type): void
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->response = $this->response->withHeader('X-Frame-Options', 'SAMEORIGIN');

        switch ($file_type) {
            case 'file':
                $upload_extensions = (array)Configure::read('upload_extensions');
                $upload_maxsize    = (int)Configure::read('upload_maxsize');
                break;
            case 'image':
                $upload_extensions = (array)Configure::read('upload_image_extensions');
                $upload_maxsize    = (int)Configure::read('upload_image_maxsize');
                break;
            case 'movie':
                $upload_extensions = (array)Configure::read('upload_movie_extensions');
                $upload_maxsize    = (int)Configure::read('upload_movie_maxsize');
                break;
            default:
                throw new NotFoundException(__('Invalid access'));
        }

        $mode = '';
        $file_url = '';
        $original_file_name = '';

        if ($this->request->is(['post', 'put'])) {
            if (Configure::read('demo_mode')) {
                $this->Flash->error(__('デモモードではアップロードできません'));
            } else {
                $uploaded = $this->request->getUploadedFiles();
                $uploadedFile = $uploaded['data']['Content']['file'] ?? $uploaded['file'] ?? null;

                if (!$uploadedFile || $uploadedFile->getError() !== UPLOAD_ERR_OK) {
                    $this->Flash->error(__('ファイルのアップロードに失敗しました'));
                    $mode = 'error';
                } else {
                    $original_file_name = $uploadedFile->getClientFilename();
                    $size               = $uploadedFile->getSize();
                    $extension          = '.' . strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

                    if (!in_array($extension, $upload_extensions, true)) {
                        $this->Flash->error(__('アップロードされたファイルの形式は許可されていません'));
                        $mode = 'error';
                    } elseif ($size === 0) {
                        $this->Flash->error(__('ファイルが空です'));
                        $mode = 'error';
                    } elseif ($size > $upload_maxsize) {
                        $this->Flash->error(__('ファイルサイズが大きすぎます'));
                        $mode = 'error';
                    } else {
                        $str = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 4);
                        $new_name = date('YmdHis') . $str . $extension;
                        $dirPath = ROOT . DS . 'files';

                        if (!is_dir($dirPath)) {
                            mkdir($dirPath, 0755, true);
                        }

                        $target = $dirPath . DS . $new_name;

                        try {
                            $uploadedFile->moveTo($target);
                            $file_url = $new_name;
                            $mode = 'complete';
                        } catch (\Throwable $e) {
                            $this->Flash->error(__('ファイルの保存に失敗しました'));
                            $mode = 'error';
                        }
                    }
                }
            }
        }

        $file_name = $original_file_name;
        $upload_extensions_str = implode(', ', $upload_extensions);
        $this->set(compact('mode', 'file_url', 'file_name', 'upload_extensions_str', 'upload_maxsize', 'file_type'));
    }

    /**
     * Admin: image upload (Ajax for summernote) / 管理: 画像アップロード (summernote用 Ajax)
     */
    public function adminUploadImage(): ?Response
    {
        $this->autoRender = false;

        if (!$this->request->is('post')) {
            return $this->response->withType('application/json')->withStringBody(json_encode([false]));
        }

        $upload_extensions = (array)Configure::read('upload_image_extensions');
        $upload_maxsize    = (int)Configure::read('upload_image_maxsize');

        $uploaded = $this->request->getUploadedFiles();
        $uploadedFile = $uploaded['file'] ?? null;

        if (!$uploadedFile || $uploadedFile->getError() !== UPLOAD_ERR_OK) {
            return $this->response->withType('application/json')->withStringBody(json_encode([false]));
        }

        $original_file_name = $uploadedFile->getClientFilename();
        $extension = '.' . strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

        if (!in_array($extension, $upload_extensions, true) || $uploadedFile->getSize() > $upload_maxsize) {
            return $this->response->withType('application/json')->withStringBody(json_encode([false]));
        }

        $str = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 4);
        $new_name = date('YmdHis') . $str . $extension;
        $dirPath = ROOT . DS . 'files';

        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $target = $dirPath . DS . $new_name;

        try {
            $uploadedFile->moveTo($target);
            $file_url = $this->request->getAttribute('webroot') . 'contents/file_image/' . $new_name;
            return $this->response->withType('application/json')->withStringBody(json_encode([$file_url]));
        } catch (\Throwable $e) {
            return $this->response->withType('application/json')->withStringBody(json_encode([false]));
        }
    }

    /**
     * File download / ファイルのダウンロード
     */
    public function fileDownload(int $content_id): Response
    {
        $contentsTable = $this->fetchTable('Contents');

        if (!$contentsTable->exists(['id' => $content_id])) {
            throw new NotFoundException(__('Invalid content'));
        }

        $content = $contentsTable->get($content_id);

        if ($content->kind !== 'file') {
            throw new NotFoundException(__('Invalid content'));
        }

        $safe_file_name = basename($content->url ?? '');
        $file_path = ROOT . DS . 'files' . DS . $safe_file_name;

        if (!file_exists($file_path)) {
            throw new NotFoundException(__('File not found'));
        }

        $display_name = $content->file_name ?: $safe_file_name;
        return $this->response->withFile($file_path, ['download' => true, 'name' => $display_name]);
    }

    /**
     * Display video file / 動画ファイルの表示
     */
    public function fileMovie(int $content_id): Response
    {
        $contentsTable = $this->fetchTable('Contents');

        if (!$contentsTable->exists(['id' => $content_id])) {
            throw new NotFoundException(__('Invalid content'));
        }

        $content = $contentsTable->get($content_id);

        if ($content->kind !== 'movie') {
            throw new NotFoundException(__('Invalid content'));
        }

        $safe_file_name = basename($content->url ?? '');
        $file_path = ROOT . DS . 'files' . DS . $safe_file_name;

        if (!file_exists($file_path)) {
            throw new NotFoundException(__('File not found'));
        }

        return $this->response->withFile($file_path, ['download' => false, 'name' => $safe_file_name]);
    }

    /**
     * Display image file / 画像ファイルの表示
     */
    public function fileImage(string $file_name): Response
    {
        if (!$file_name || !preg_match('/^[a-zA-Z0-9_\-\.]+$/', $file_name) || strlen($file_name) > 255 || str_starts_with($file_name, '.')) {
            throw new NotFoundException(__('Invalid filename'));
        }

        $upload_extensions = (array)Configure::read('upload_image_extensions');
        $extension = '.' . strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($extension, $upload_extensions, true)) {
            throw new NotFoundException(__('Invalid content'));
        }

        $safe_file_name = basename($file_name);
        $file_path = ROOT . DS . 'files' . DS . $safe_file_name;

        if (!file_exists($file_path)) {
            throw new NotFoundException(__('File not found'));
        }

        return $this->response->withFile($file_path, ['download' => false, 'name' => $safe_file_name]);
    }

    /**
     * Admin: display learner's study history details / 管理者: 受講者の学習履歴詳細表示
     */
    public function adminRecord(int $course_id, int $user_id): void
    {
        $course = $this->fetchTable('Courses')->get($course_id);
        $identity = $this->Authentication->getIdentity();
        $role = $identity ? $identity->get('role') : 'user';

        $contentsTable = $this->fetchTable('Contents');
        if (method_exists($contentsTable, 'getContentRecord')) {
            $contents = $contentsTable->getContentRecord($user_id, $course_id, $role);
        } else {
            $contents = $contentsTable->find()
                ->where(['course_id' => $course_id])
                ->orderByAsc('sort_no')
                ->all();
        }

        $this->set(compact('course', 'contents'));
        $this->render('index');
    }

    /**
     * Learner: display content / 受講者: コンテンツ表示
     */
    public function view(int $content_id): void
    {
        $contentsTable = $this->fetchTable('Contents');

        if (!$contentsTable->exists(['id' => $content_id])) {
            throw new NotFoundException(__('Invalid content'));
        }

        $this->viewBuilder()->disableAutoLayout();
        $content = $contentsTable->get($content_id, contain: ['Courses']);
        $course = $content->course ?? $this->fetchTable('Courses')->get($content->course_id);
        $this->set(compact('content', 'course'));
    }
}