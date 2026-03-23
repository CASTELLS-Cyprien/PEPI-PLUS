<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class DocController extends AbstractController
{
    #[Route('/admin/doc/{path}', name: 'app_api_doc', requirements: ['path' => '.+'])]
    public function displayDoc(string $path = 'index.html'): BinaryFileResponse
    {
        // Chemin vers le dossier où tu as généré la doc
        $docDir = $this->getParameter('kernel.project_dir') . '/var/api-docs/';
        
        return new BinaryFileResponse($docDir . $path);
    }
}