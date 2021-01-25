<?php

namespace App\Controller\Admin;

use App\Repository\ImportRepository;
use App\Tools\Block;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminController extends AbstractController
{
    private $translator;
    private $importRepository;
    private $kernel;

    public function __construct(TranslatorInterface $translator, ImportRepository $importRepository, KernelInterface $kernel)
    {
        $this->translator = $translator;
        $this->importRepository = $importRepository;
        $this->kernel=$kernel;
    }

    /**
     * @Route("/import/codes", name="import_codes")
     */
    public function importCodesActions(Request $request)
    {
        // retrieve the file with the name given in the form.
        // do var_dump($request->files->all()); if you need to know if the file is being uploaded.
        $file = $request->files->get('uploadedfile');

        $status = array('status' => "success", "fileUploaded" => false);

        // If a file was uploaded
        if (!empty($file)) {
            // generate a random name for the file but keep the extension
            $filename = uniqid('', true).'-'.$file->getClientOriginalName();
            $publicPath = $this->getParameter('kernel.project_dir') . '/public/';
            $path = $publicPath . 'data/';

            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true) && !is_dir($path)) {
                    return new JsonResponse(['status' => 'fail', 'errors' => sprintf($this->translator->trans('Directory "%s" was not created'), $path)]);
                }
            }
            $import=$this->importRepository->findOneBy(['fileName'=>$filename, 'status'=>'PENDING']);
            if(!$import){
                $this->importRepository->create($filename);
            }
            $filePath = $path . $filename;

            try {
                $file->move($path, $filename); // move the file to a path
            } catch (\Throwable $e) {
                return new JsonResponse(['status' => 'fail', 'errors' => $e->getMessage()]);
            }
            if (!file_exists($path . $filename)) {
                return new JsonResponse(['status' => 'fail', 'errors' => $this->translator->trans('file is not found')]);
            }
            $status = array('status' => "success", "fileUploaded" => true, 'file' => $filePath, 'filename'=>$filename);

        }
        return new JsonResponse($status);
    }

    /**
     * import codes.
     *
     * @Route("/import-codes", name="import-data")
     * @param Request $request
     * @return Response
     */
    public function importDataAction(Request $request)
    {
        $csvFile = $request->request->get('file', '');

        $shipToken = $request->request->get('token', '');
        $fileName = $request->request->get('filename', '');

        $tokenShipContent = json_encode([]);

        if (!$csvFile) {
            return new Response($tokenShipContent, 200, ['Content-Type' => 'application/json']);
        }

        $block = new Block($shipToken);
         $i=0;
        if ($block->read() === 404 && $i<2) {
            $i++;

                $application = new Application($this->kernel);
                $application->setAutoExit(false);

                $input = new ArrayInput([
                    'command' => 'create-user:serialNumber',
                    // (optional) define the value of command arguments
                    'filename' => $csvFile,
                    'token' => $shipToken,
                ]);

                // You can use NullOutput() if you don't need the output
                $output = new BufferedOutput();
                $application->run($input, $output);

                // return the output, don't use if you used NullOutput()
                $content = $output->fetch();

            // return new Response(""), if you used NullOutput()
//            return new Response($content);
//            $cmd = "/usr/bin/php " . $this->getParameter('kernel.project_dir') . "/../bin/console create-user:serialNumber $csvFile $shipToken > /dev/null 2>&1 &";
//
//            $process = Process::fromShellCommandline($cmd);
////            $process->disableOutput();
//            $process->run();

            return new Response($content, 200, ['Content-Type' => 'application/json']);
        }

        $tokenShipContent = $block->read();

        if (in_array('!END!', json_decode($tokenShipContent))) {
            $block->delete();//Cleaning the memory

                $import=$this->importRepository->findOneBy(['fileName'=>$fileName, 'status'=>'PENDING']);
                if(!$import){
                    $import=$this->importRepository->create($fileName);
                }
                $this->importRepository->update($import, 'COMPLETE');

        }


        return new Response($tokenShipContent, 200, ['Content-Type' => 'application/json']);
    }
}
