<?php

namespace Hgabka\SeoBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Hgabka\SeoBundle\Entity\Seo;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

class SeoAdminController extends CRUDController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;

    /** @var TranslatorInterface */
    protected TranslatorInterface $translator;

    #[Required]
    public function setDoctrine(ManagerRegistry $doctrine): self
    {
        $this->doctrine = $doctrine;

        return $this;
    }

    #[Required]
    public function setTranslator(TranslatorInterface $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    public function createAction(Request $request): Response
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        $repo = $this->doctrine->getRepository(Seo::class);
        $seo = $repo->findGeneral();

        $isSaved = true;
        if (!$seo) {
            $seo = new Seo();
            $seo
                ->setRef(null)
            ;
            $isSaved = false;
        }
        $this->admin->checkAccess('edit', $seo);

        $existingObject = $seo;
        $preResponse = $this->preEdit($request, $existingObject);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($existingObject);
        $objectId = $this->admin->getNormalizedIdentifier($existingObject);

        $form = $this->admin->getForm();

        if (!\is_array($fields = $form->all()) || 0 === \count($fields)) {
            throw new \RuntimeException('No editable field defined. Did you forget to implement the "configureFormFields" method?');
        }

        $form->setData($existingObject);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid) {
                $submittedObject = $form->getData();
                $this->admin->setSubject($submittedObject);

                try {
                    $existingObject = $this->admin->update($submittedObject);

                    if ($this->isXmlHttpRequest($request)) {
                        return $this->renderJson([
                            'result' => 'ok',
                            'objectId' => $objectId,
                            'objectName' => $this->escapeHtml($this->admin->toString($existingObject)),
                        ], 200, []);
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_edit_success',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'SonataAdminBundle'
                        )
                    );

                    // redirect to edit mode
                    return $this->redirectTo($request, $existingObject);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash('sonata_flash_error', $this->trans('flash_lock_error', [
                        '%name%' => $this->escapeHtml($this->admin->toString($existingObject)),
                        '%link_start%' => '<a href="' . $this->admin->generateObjectUrl('edit', $existingObject) . '">',
                        '%link_end%' => '</a>',
                    ], 'SonataAdminBundle'));
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest($request)) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->translator->trans(
                            'flash_edit_error',
                            ['%name%' => $this->escapeHtml($this->admin->toString($existingObject))],
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested($request)) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $formView = $form->createView();
        $template = $this->admin->getTemplateRegistry()->getTemplate($templateKey);

        if (!$isSaved) {
            $this->addFlash(
                'sonata_flash_info',
                $this->translator->trans('hg_seo.seo.warning')
            );
        }

        return $this->renderWithExtraParams($template, [
            'action' => 'edit',
            'form' => $formView,
            'object' => $existingObject,
            'objectId' => $objectId,
        ], null);
    }

    public function editAction(Request $request): Response
    {
        return $this->redirect($this->admin->generateUrl('create'));
    }

    public function listAction(Request $request): Response
    {
        return $this->redirectToRoute('sonata_admin_dashboard');
    }

    public function deleteAction(Request $request): Response
    {
        return $this->redirect($this->admin->generateUrl('create'));
    }
}
