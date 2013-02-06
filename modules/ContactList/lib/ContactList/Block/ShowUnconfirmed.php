<?php
/**
 * ContactList
 *
 * @copyright Florian Schießl, Carsten Volmer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package ContactList
 * @author Florian Schießl <info@ifs-net.de>.
 * @link http://www.ifs-net.de
 */

class ContactList_Block_ShowUnconfirmed extends Zikula_Controller_AbstractBlock
{
    /**
     * Initialise block.
     *
     * @return void
     */
    public function init()
    {
        SecurityUtil::registerPermissionSchema('ContactList::', 'ShowUnconfirmed::');
    }

    /**
     * Post-construction initialization.
     *
     * @return void
     */
    protected function postInitialize()
    {
        // Disable caching by default.
        $this->view->setCaching(Zikula_View::CACHE_DISABLED);
    }


    /**
     * Get information on block.
     *
     * @return array The block information
     */
    public function info()
    {
        return array(
            'module'         => $this->name,
            'text_type'      => $this->__('ShowUnconfirmed'),
            'text_type_long' => $this->__("Show new buddies awaiting your acception"),
            'allow_multiple' => true,
            'form_content'   => false,
            'form_refresh'   => false,
            'show_preview'   => true,
        );
    }

    /**
     * Display block.
     *
     * @param array $blockInfo A blockinfo structure.
     *
     * @return string|void The rendered block.
     */
    public function display($blockInfo)
    {
        if (!SecurityUtil::checkPermission('ContactList::', $blockInfo['title']."::", ACCESS_READ) || !ModUtil::available('ContactList') || UserUtil::isGuestUser()) {
            return;
        }


        $buddies = pnModAPIFunc('ContactList','user','getall',
            array(  'bid'       => UserUtil::getVar('uid'),
                    'state'     => 0,
                    'sort'      => 'uname'
                )
            );
        if (!(count($buddies) > 0 )) {
            return;
        } else {
            $this->view->assign('buddies_unconfirmed', $buddies);
            $blockinfo['title'] = $this->__('Contact requests');
            $blockInfo['content'] = $this->view->fetch('block/showunconfirmed.htm');
            return BlockUtil::themeBlock($blockInfo);
        }
    }
}