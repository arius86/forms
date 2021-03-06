<?php
/**
 * This file is part of Vegas package
 *
 * @author Arkadiusz Ostrycharz <aostrycharz@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage http://vegas-cmf.github.io/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Vegas\Tests\Forms\Element;

use Phalcon\DI;
use Phalcon\Forms\Element\Text;
use Vegas\Forms\Element\Datepicker;
use Vegas\Tests\Stub\Models\FakeModel;
use Vegas\Tests\Stub\Models\FakeVegasForm;

class DatepickerTest extends \PHPUnit_Framework_TestCase
{
    protected $di;
    protected $form;
    protected $model;

    protected function setUp()
    {
        $this->di = DI::getDefault();
        $this->model = new FakeModel();
        $this->form = new FakeVegasForm();

        $datepicker = new Datepicker('date');
        $datepicker->setAttribute('class', 'test1');
        $this->form->add($datepicker);
    }

    public function testInput()
    {
        $dateTime = new \DateTime(date('Y-m-d'));

        $formattedDate = $dateTime->format('Y-m-d');

        $this->form->bind(array('date' => $formattedDate), $this->model);
        $this->assertEquals($dateTime->getTimestamp(), $this->model->date);
        $this->assertEquals($this->form->get('date')->getValue(), $formattedDate);

        // create new form for filled model
        $this->form = new FakeVegasForm($this->model);
        $datepicker = new Datepicker('date');
        $this->form->add($datepicker);
        $this->assertEquals($this->form->get('date')->getValue(), $formattedDate);

        // treat nondate values as normal string
        $testString = 'test string';
        $this->form->bind(array('date' => $testString), $this->model);
        $this->assertEquals($testString, $this->model->date);

        // create new form for filled model
        $this->form = new FakeVegasForm($this->model);
        $datepicker = new Datepicker('date');
        $this->form->add($datepicker);
        $this->form->bind(array('date' => $formattedDate), $this->model);

        $this->assertEquals($formattedDate, $this->form->get('date')->getValue());
        $this->assertEquals($dateTime->getTimestamp(), $this->model->date);
    }

    public function testRender()
    {
        $testElement = (new Text('date'))
            ->setAttribute('class', 'test1');

        $this->assertEquals(
            $testElement->render(),
            $this->form->get('date')->render()
        );

        $attributes = ['name' => 'foobaz'];

        $this->assertEquals($testElement->render($attributes), $this->form->get('date')->render($attributes));

        $this->regenerateForm();
        $this->form->get('date')->getDecorator()->setTemplateName('jquery');

        $this->assertEquals('<input type="text" id="date" name="date" class="test1" value="2014-03-12" vegas-datepicker />', $this->form->get('date')->renderDecorated());
        $this->assertEquals('<input type="text" id="date" name="date" class="test1" value="2012-04-11" vegas-datepicker />', $this->form->get('date')->renderDecorated(['value' => '2012-04-11']));
    }

    private function regenerateForm()
    {
        $this->model->content = '#abcdef';
        $this->form = new FakeVegasForm($this->model);

        $datepicker = new Datepicker('date');
        $datepicker->setAttribute('class', 'test1');
        $datepicker->getDecorator()->setDI($this->di);

        $this->form->add($datepicker);
        $this->form->bind(['date' => '2014-03-12'], $this->model);
    }
}
