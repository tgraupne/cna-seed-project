<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2014 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2014. All rights reserved".
     ********************************************************************************/

    class ContactsRelatedListPortletTest extends ZurmoBaseTest
    {
            public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            SecurityTestHelper::createSuperAdmin();
            $super = User::getByUsername('super');
            Yii::app()->user->userModel = $super;

            //Setup test data owned by the super user.
            $account = AccountTestHelper::createAccountByNameForOwner('superAccount', $super);
            AccountTestHelper::createAccountByNameForOwner           ('superAccount2', $super);
            ContactTestHelper::createContactWithAccountByNameForOwner('superContact', $super, $account);
        }

        public function testSaveAndRetrievePortlet()
        {
            $user = UserTestHelper::createBasicUser('Billy');
            $contacts = Contact::getByName('superContact superContactson');
            $portlet = new Portlet();
            $portlet->column    = 2;
            $portlet->position  = 5;
            $portlet->layoutId  = 'Test';
            $portlet->collapsed = true;
            $portlet->viewType  = 'ContactsForAccountRelatedList';
            $portlet->serializedViewData = serialize(array('title' => 'Testing Title'));
            $portlet->user      = $user;
            $this->assertTrue($portlet->save());
            $portlet = Portlet::getById($portlet->id);
            $params = array(
                'controllerId'         => 'test',
                'relationModuleId'     => 'test',
                'relationModel'        => $contacts[0],
                'redirectUrl'          => 'someRedirect',
            );
            $portlet->params = $params;
            $unserializedViewData = unserialize($portlet->serializedViewData);
            $this->assertEquals(2,                     $portlet->column);
            $this->assertEquals(5,                     $portlet->position);
            $this->assertEquals('Testing Title',       $portlet->getTitle());
            $this->assertEquals(false,                 $portlet->isEditable());
            $this->assertEquals('Test',                $portlet->layoutId);
            //$this->assertEquals(true,                  $portlet->collapsed); //reenable once working
            $this->assertEquals('ContactsForAccountRelatedList', $portlet->viewType);
            $this->assertEquals($user->id,             $portlet->user->id);
            $view = $portlet->getView();
        }
    }
?>
