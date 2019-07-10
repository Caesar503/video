<?php

namespace App\Admin\Controllers;

use App\Model\Up;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UpController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '文件管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Up);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('path', __('Path'));
        $grid->column('add_time', __('Add time'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Up::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('path', __('Path'));
        $show->field('add_time', __('Add time'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Up);

        $form->text('title', __('name'));
        $form->file('path', __('file'))->uniqueName();
//        $form->number('add_time', __('Add time'));

        return $form;
    }
}
