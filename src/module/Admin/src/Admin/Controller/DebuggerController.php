<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Admin\Controller;

use Admin\Form\DebuggerForm;
use Ui\View\Helper\Encrypt;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DebuggerController extends AbstractActionController
{
    public function indexAction()
    {
        $this->assertGranted('debugger.use');

        $form    = new DebuggerForm();
        $message = false;

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $helper  = new Encrypt();
                $message = $helper->decrypt($form->get('message')->getValue());
            }
        }

        $view = new ViewModel(['form' => $form, 'message' => $message]);
        $view->setTemplate('admin/debugger');
        return $view;
    }
}
