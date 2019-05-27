<?php

namespace App\Admin\Controllers;

use App\MakeOrder;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class MakeOrderController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MakeOrder);

        $grid->id('Id');
        $grid->house_id('House id');
        $grid->agent_id('Agent id');
        $grid->make_id('Make id');
        $grid->make_name('Make name');
        $grid->make_mobile('Make mobile');
        $grid->ID_card('ID card');
        $grid->time('Time');
        $grid->time_slot('Time slot');
        $grid->remark('Remark');
        $grid->is_divide('Is divide');
        $grid->state('State');
        $grid->add_schedule('Add schedule');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');

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
        $show = new Show(MakeOrder::findOrFail($id));

        $show->id('Id');
        $show->house_id('House id');
        $show->agent_id('Agent id');
        $show->make_id('Make id');
        $show->make_name('Make name');
        $show->make_mobile('Make mobile');
        $show->ID_card('ID card');
        $show->time('Time');
        $show->time_slot('Time slot');
        $show->remark('Remark');
        $show->is_divide('Is divide');
        $show->state('State');
        $show->add_schedule('Add schedule');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MakeOrder);

        $form->number('house_id', 'House id');
        $form->number('agent_id', 'Agent id');
        $form->number('make_id', 'Make id');
        $form->text('make_name', 'Make name');
        $form->text('make_mobile', 'Make mobile');
        $form->text('ID_card', 'ID card');
        $form->date('time', 'Time')->default(date('Y-m-d'));
        $form->switch('time_slot', 'Time slot');
        $form->text('remark', 'Remark');
        $form->switch('is_divide', 'Is divide');
        $form->switch('state', 'State');
        $form->switch('add_schedule', 'Add schedule');

        return $form;
    }
}
