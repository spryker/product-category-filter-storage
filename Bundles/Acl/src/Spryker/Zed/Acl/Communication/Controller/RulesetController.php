<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Acl\Communication\Controller;

use Spryker\Zed\Acl\Business\AclFacade;
use Spryker\Zed\Acl\Communication\AclCommunicationFactory;
use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @method AclCommunicationFactory getFactory()
 * @method AclFacade getFacade()
 */
class RulesetController extends AbstractController
{

    const ROLE_UPDATE_URL = '/acl/role/update?id-role=%d';

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $idRule = $request->get('id-rule');
        $idRole = $request->get('id-role');

        if (empty($idRule)) {
            $this->addErrorMessage('Missing rule id!');

            return $this->redirectResponse(sprintf(self::ROLE_UPDATE_URL, $idRole));
        }

        $removeStatus = $this->getFacade()->removeRule($idRule);

        if ($removeStatus) {
            $this->addSuccessMessage(sprintf('Rule with id "%d" was successfully removed!', $idRule));
        } else {
            $this->addErrorMessage('Failed to remove rule');
        }

        return $this->redirectResponse(sprintf(self::ROLE_UPDATE_URL, $idRole));
    }

}