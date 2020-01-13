<?php


namespace JustForFun\Portal\Admin\Controller;


use XF\Admin\Controller\AbstractController;

class JustForFunLogs extends AbstractController
{
    public function actionIndex()
    {
        $page = $this->filterPage();
        $perPage = 20;

        /** @var JustForFun\Portal\Repository\ServerActionLog $modLogRepo */
        $modLogRepo = $this->repository('JustForFun\Portal:ServerActionLog');

        $logFinder = $modLogRepo->findLogsForList()
            ->limitByPage($page, $perPage);

        $linkFilters = [];
        if ($userId = $this->filter('user_id', 'uint'))
        {
            $linkFilters['user_id'] = $userId;
            $logFinder->where('user_id', $userId);
        }

        if ($this->isPost())
        {
            // redirect to give a linkable page
            return $this->redirect($this->buildLink('logs/logs/server-actions', null, $linkFilters));
        }

        $viewParams = [
            'entries' => $logFinder->fetch(),
            'logUsers' => $modLogRepo->getUsersInLog(),

            'userId' => $userId,

            'page' => $page,
            'perPage' => $perPage,
            'total' => $logFinder->total(),
            'linkFilters' => $linkFilters
        ];

        return $this->view('JustForFun\Moderator\Log', 'log_server_actions', $viewParams);
    }
}