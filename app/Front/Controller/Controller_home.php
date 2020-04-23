<?php

class Controller_home extends Controller
{

    use ControllerTrait;

    /**
     * 表示
     */
    public function action_index() : void
    {
        try {

            $view = View::forge();
            $view->display('home/index.tpl');

        } catch (Throwable $e) {
            fatalLog('想定外の例外が発生しました。', $e);
            $this->showSystemError();
        }
    }

}
