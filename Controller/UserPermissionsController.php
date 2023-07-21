<?php

namespace KimaiPlugin\ApiUserPermissionsBundle\Controller;

use App\API\BaseApiController;
use App\Entity\Timesheet;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/users')]
#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
final class UserPermissionsController extends BaseApiController
{
    public function __construct(
        private ViewHandlerInterface $viewHandler,
    ) {
    }

    #[Rest\Get(path: '/me/permissions', name: 'me_user_permissions')]
    #[ApiSecurity(name: 'apiUser')]
    #[ApiSecurity(name: 'apiToken')]
    public function mePermissionsAction(): Response
    {
        $user = $this->getUser();

        $timesheet = new Timesheet();
        $timesheet->setUser($user);

        $isGranted = [
            'own_timesheet' => [
                'create'        => $this->isGranted('create_own_timesheet'),
                'edit'          => $this->isGranted('edit', $timesheet),
                'edit_rate'     => $this->isGranted('edit_rate', $timesheet),
                'edit_export'   => $this->isGranted('edit_export', $timesheet),
                'edit_billable' => $this->isGranted('edit_billable', $timesheet),
                'delete'        => $this->isGranted('delete', $timesheet),
            ],
        ];

        $view = new View($isGranted, 200);

        return $this->viewHandler->handle($view);
    }

}
