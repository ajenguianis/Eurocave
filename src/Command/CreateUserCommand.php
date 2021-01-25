<?php

namespace App\Command;


use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use App\Services\EncryptService;
use App\Tools\Block;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    use LockableTrait;

    private $logger;
    public $_logImport = [];
    private $passwordEncoder;
    private $userRepository;
    private $groupRepository;

    public function __construct(LoggerInterface $logger, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, GroupRepository $groupRepository)
    {
        $this->logger = $logger;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('create-user:serialNumber')
            ->addArgument('filename', InputArgument::REQUIRED, 'the file name')
            ->addArgument('token', InputArgument::REQUIRED, 'the token')
            // the short description shown while running "php bin/console list"
            ->setDescription('Create user with serial number.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Create user with serial number.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        if (!$this->lock()) {
//            $output->writeln('The command is already running in another process.');
//
//            return 0;
//        }
        $filename = $input->getArgument('filename');
        $token = $input->getArgument('token');
        $logger = $this->logger;
        $logger->info("******************* START IMPORT=> " . date('d/m/Y H:i:s') . "  *******" . $filename);
        $this->_logImport[] = '!START!';
        $block = new Block($token);

        if (($handle = fopen($filename, "r")) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $row++;
                if ($row === 1) {
                    $header = $data;
                    continue;
                }

                if (count($header) !== count($data)) {
                    continue;
                }
                try {
                    $code = array_combine($header, $data);
                } catch (\Throwable $e) {

                }

                $serial = current($data);
                $serial = trim(preg_replace('/\s+/', '', $serial));


                if (isset($serial) && !empty($serial)) {

                    try {
                        $user = $this->userRepository->findOneBy(['serialNumber' => EncryptService::encodeData($serial)]);

                        if (!$user) {
                            $user = new User();
                        }

                        $user->setEmail('code-' . sha1($serial) . '@eurocave.fr');
                        $user->setPassword(
                            $this->passwordEncoder->encodePassword(
                                $user,
                                $serial
                            )
                        );
                        $user->setRoles(['ROLE_USER']);
                        $group = $this->groupRepository->findOneByName('Utilisateur final');
                        $user->setGroup($group);
                        $user->setSerialNumber($serial);
                        $this->userRepository->save($user);
                        $this->_logImport[] = 'SERIAL: ' . $serial;
                    } catch (\Throwable $e) {
                        $this->_logImport[] = $e->getMessage();
                        continue;
                    }


                }
                $block->write(json_encode($this->_logImport));
            }
            fclose($handle);
            $this->_logImport[] = '!END!';
            $this->_logImport[] = 'FIN DE TRAITEMENT';
            $block->write(json_encode($this->_logImport));
        }
        return Command::SUCCESS;

// or return this if some error happened during the execution
// (it's equivalent to returning int(1))
// return Command::FAILURE;
    }
}