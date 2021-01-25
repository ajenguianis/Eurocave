<?php


namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $passwordEncoder;
    private $resetPasswordHelper;
    private $mailer;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ResetPasswordHelperInterface $resetPasswordHelper, MailerInterface $mailer)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => ['changeUserPassword'],
            BeforeEntityPersistedEvent::class => ['setUserPassword'],
            AfterEntityPersistedEvent::class => ['sendEmail'],
        ];
    }

    public function changeUserPassword(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }


        if ($entity->getSerialNumber()) {
            $entity->setEmail('code-' . Sha1($entity->getSerialNumber()) . '@eurocave.fr');
            $entity->setPassword(
                $this->passwordEncoder->encodePassword(
                    $entity,
                    $entity->getSerialNumber()
                )
            );
        }
        if (!$entity->getPassword()) {
            $entity->setPassword(
                $this->passwordEncoder->encodePassword(
                    $entity,
                    $this->randomPassword()
                )
            );
        }
    }

    public function setUserPassword(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }
        if (!$entity->getPassword()) {
            $entity->setPassword(
                $this->passwordEncoder->encodePassword(
                    $entity,
                    $this->randomPassword()
                )
            );
        }
    }

    public function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * @param AfterEntityPersistedEvent $event
     * @throws ResetPasswordExceptionInterface
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
     public function sendEmail(AfterEntityPersistedEvent $event){
         $entity = $event->getEntityInstance();

         if (!($entity instanceof User)) {
             return;
         }
         $this->processSendingPasswordResetEmail($entity);
     }
    /**
     * @param User $user
     * @return User
     * @throws ResetPasswordExceptionInterface
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private function processSendingPasswordResetEmail(User $user)
    {

        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@eurocave.com', 'EuroCave'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ]);

        $this->mailer->send($email);
        return $user;
    }
}