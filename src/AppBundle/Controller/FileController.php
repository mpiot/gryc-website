<?php
// src/AppBundle/Controller/FileController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class FileController.
 * 
 * @route("/files")
 */
class FileController extends Controller
{
    /**
     * @Route("/{strainName}-{featureType}-{molType}-{format}.zip", name="file_downloadZipFlatFile")
     */
    public function downloadZipFlatFileAction($strainName, $featureType, $molType, $format)
    {
        $em = $this->getDoctrine()->getManager();
        $strain = $em->getRepository('AppBundle:Strain')->findOneByName($strainName);
        $files = $em->getRepository('AppBundle:FlatFile')->findByStrainFeatureMolFormat($strainName, $featureType, $molType, $format);

        if (null === $strain) {
            throw $this->createNotFoundException("This strain doen't exists.");
        }

        $this->denyAccessUnlessGranted('VIEW', $strain);

        $zipname = $this->get('kernel')->getRootDir().'/../protected-files/temp/'.uniqid().'.zip';

        if (!$zip = new \ZipArchive()) {
            throw new \Exception('The zip file can\'t be create.');
        }

        if ($zip->open($zipname, \ZipArchive::CREATE)) {
            foreach ($files as $file) {
                $zip->addFile($file->getAbsolutePath(), $file->getChromosome()->getName().'-'.$featureType.'-'.$molType.'.'.$format);
            }
            $zip->close();
        } else {
            throw new \Exception('The zip file can\'t be open.');
        }

        // Clear the cache because the file may be send without content when we make it in the same request
        clearstatcache(false, $zipname);

        // Here we don't use the X-Accel-Redirect, because the file isn't static, we delete it just after PHP make it, and nginx take it in charge
        $response = new BinaryFileResponse($zipname);
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$strainName.'-'.$featureType.'-'.$molType.'-'.$format.'.zip"');
        $response->headers->set('Cache-Control', 'private');
        // Delete the file
        $response->deleteFileAfterSend(true);

        return $response;
    }

    /**
     * @Route("/{chromosomeName}-{featureType}-{molType}.{format}", name="file_downloadFlatFile")
     */
    public function downloadFlatFileAction(Request $request, $chromosomeName, $featureType, $molType, $format)
    {
        $em = $this->getDoctrine()->getManager();
        $file = $em->getRepository('AppBundle:FlatFile')->findOneByFeatureMolChromosomeFormat($featureType, $molType, $chromosomeName, $format);

        if (null === $file) {
            throw $this->createNotFoundException("Ce fichier n'existe pas.");
        }

        $this->denyAccessUnlessGranted('VIEW', $file->getChromosome()->getStrain());

        $request->headers->set('X-Sendfile-Type', 'X-Accel-Redirect');
        $request->headers->set('X-Accel-Mapping', '/home/docker/protected-files/=/protected_files/');

        BinaryFileResponse::trustXSendfileTypeHeader();
        $response = new BinaryFileResponse($file->getAbsolutePath());
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$chromosomeName.'-'.$featureType.'-'.$molType.'.'.$format.'"');
        $response->headers->set('Cache-Control', 'private');

        return $response;
    }
}