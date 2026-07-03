<?php

namespace App\Twig\Components;

use App\Entity\Build;
use App\Form\CommentType;
use App\Service\BuildService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;


#[AsLiveComponent]
final class CommentSection
{

    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public Build $build;

    private ?Comment $commentEntity = null;

    public function __construct(
        private Security $security,
        private FormFactoryInterface $formFactory,
        private BuildService $buildService,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $this->commentEntity ??= (new Comment())
            ->setBuild($this->build)
            ->setAuthor($user);

        return $this->formFactory->create(CommentType::class, $this->commentEntity);
    }

    #[LiveAction]
    public function comment(): void
    {
        $this->submitForm();

        $comment = $this->getForm()->getData();
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }

        $this->buildService->addComment($this->build, $comment, $user);

        $this->resetForm(); 
        $this->commentEntity = null;
    }

}
