<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {

        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route("/")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product, [
            'method' => 'POST',
        ]);

//        $form->handleRequest($request);

        if ($request->getMethod() === Request::METHOD_POST) {
            dd($request);
        }

//        if ($form->isSubmitted() && $form->isValid()) {
//            /** @var UploadedFile $brochureFile */
//            $brochureFile = $form->get('brochure')->getData();
//
//            // this condition is needed because the 'brochure' field is not required
//            // so the img file must be processed only when a file is uploaded
//            if ($brochureFile) {
//                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
//                // this is needed to safely include the file name as part of the URL
//                $safeFilename = $slugger->slug($originalFilename);
//                $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $brochureFile->guessExtension();
//
//                // Move the file to the directory where brochures are stored
//                try {
//                    $brochureFile->move(
//                        $this->parameterBag->get('media'),
//                        $newFilename
//                    );
//                } catch (FileException $exception) {
//                    die('x');
//                }
//
//                $product->setBrochureFilename($newFilename);
//            }
//
//
//            return $this->redirectToRoute('app_default_index');
//        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/handle-file", methods={"POST"})
     */
    public function handleFile(Request $request)
    {
        if ($request->getMethod() === Request::METHOD_POST) {
            $fileId = $request->request->get('dzuuid');
            $chunkIndex = (int)$request->request->get('dzchunkindex') + 1;
            $chunkTotal = (int)$request->request->get('dztotalchunkcount');

            /** @var UploadedFile $file */
            $file = $request->files->get('file');

            $targetPath = $this->parameterBag->get('kernel.project_dir') . '/assets/media/tmpfolder/';
            $fileType = $file->getClientOriginalExtension();
            $filename = vsprintf('%s-%s.%s', [
                $fileId,
                $chunkIndex,
                $fileType,
            ]);
            $targetFile = $targetPath . $filename;

            $returnResponse = function ($info = null, $filelink = null, $status = "error") {
                die (json_encode(array(
                    "status" => $status,
                    "info" => $info,
                    "file_link" => $filelink
                )));
            };

            $file->move(
                $targetPath,
                $filename
            );
// Be sure that the file has been uploaded
            if (!file_exists($targetFile)) {
                $returnResponse("An error occurred and we couldn't upload the requested file.");
            }

            /* ========================================
              FINAL UPLOAD CONDITIONAL
            ======================================== */
            if ($chunkIndex === $chunkTotal) {


                // ===== concatenate uploaded files =====
                // set emtpy string for file content concatonation
                $file_content = "";
                
                // loop through temp files and grab the content
                for ($i = 1; $i <= $chunkTotal; $i++) {

                    // target temp file
                    $temp_file_path = realpath("{$targetPath}{$fileId}-{$i}.{$fileType}") or $returnResponse("Your chunk was lost mid-upload.");
                    // ^^^^^^^ this is where the failure is occurring, $i = 1, so first iteration

                    // copy chunk...you'll see a bunch of methods included below that I've tried, but the simplest one is method 3, so I've tested mostly there
                    // method 1
                    /*$temp_file = fopen($temp_file_path, "rb") or $returnResponse("The server cannot open your chunks");
                    $chunk = base64_encode(fread($temp_file, $fileSize));
                    fclose($temp_file);
                    // method 2
                    $chunk = base64_encode(stream_get_contents($temp_file_path, $fileSize));*/
                    // method 3
//                    $chunk = base64_encode(file_get_contents($temp_file_path));
                    $chunk = file_get_contents($temp_file_path);

                    // check chunk content
                    if (empty($chunk)) {
                        $returnResponse("Chunks are uploading as empty strings.");
                    }

                    // add chunk to main file
                    $file_content .= $chunk;

                    // delete chunk
                    unlink($temp_file_path);
                    if (file_exists($temp_file_path)) {
                        $returnResponse("Your temp files could not be deleted.");
                    }
                }
//                file_put_contents("{$targetPath}{$fileId}.{$fileType}", base64_decode($file_content));
                file_put_contents("{$targetPath}{$fileId}.{$fileType}", $file_content);

                $returnResponse(null, null, "final return");
            } else {
                $returnResponse(null, null, "chunksending not reached");
            }
        }

    }
}
