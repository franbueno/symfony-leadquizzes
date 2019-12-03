<?php

namespace AppBundle\Command;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\User;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class GenerateFakeDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:generate-fake-data')
            // the short description shown while running "php bin/console list"
            ->setDescription('Generates data with Faker library.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to generate a fake data ...');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = realpath(__DIR__ . '/../../../bin');
        passthru(sprintf('php %s/console doctrine:database:drop --force ', $path));
        passthru(sprintf('php %s/console doctrine:database:create ', $path));
        passthru(sprintf('php %s/console doctrine:schema:update --force ', $path));
        $em = $this->getContainer()->get('doctrine')->getManager();
        $generator = Factory::create();
        $populator = new Populator($generator, $em);

        $populator->addEntity(Quiz::class, 7, [
            'name'     => function () {
                $names = [
                    'Adrenal Fatigue Quiz',
                    'What Is Your Business Strength Persona',
                    'Metabolism Type Quiz',
                    'Employee Evaluation Form',
                    'How Many Of These Words Do You Actually Know',
                    'Do You Have A Good Work/Life Balance',
                    'Which US Region Should You Visit',
                ];
                return $names[array_rand($names)];
            },
            'title'     => function () {
                $titles = [
                    'Adrenal Fatigue Quiz?',
                    'What Is Your Business Strength Persona?',
                    'Metabolism Type Quiz?',
                    'Employee Evaluation Form?',
                    'How Many Of These Words Do You Actually Know?',
                    'Do You Have A Good Work/Life Balance?',
                    'Which US Region Should You Visit?',
                ];
                return $titles[array_rand($titles)];
            },
            'description' => function () {
                return 'Lorem fistrum apetecan no te digo trigo por no llamarte Rodrigor ut fistro ese pedazo de.';
            },
            'action'      => function () {
                return 'Start Quiz';
            },
            'image'      => function () {
                $images = [
                    '/uploads/quiz/quiz-5de2a958c10a1.jpg',
                    '/uploads/quiz/quiz-5de2a958c10a2.jpg',
                    '/uploads/quiz/quiz-5de2aaa5e3be5.jpg',
                    '/uploads/quiz/quiz-5de2aab58702d.jpg',
                    '/uploads/quiz/quiz-5de2afbe55d0a.jpg',
                    '/uploads/quiz/quiz-5de2afbe55d0v.jpg',
                    '/uploads/quiz/quiz-5de2afbe55d21.jpg',
                ];
                return $images[array_rand($images)];
            },
            'createdAt'      => function () {
                return new \DateTime();
            },
            'modifiedAt'      => function () {
                return new \DateTime();
            }
        ]);
        $populator->addEntity(User::class, 1, [
            'avatar' => 'https://api.adorable.io/avatars/64/abott@adorable.png'
        ]);
        $populator->execute();

//         $userManager = $this->get('fos_user.user_manager');
//         $user = $userManager->createUser();
//         $user = new User;
//         $username = 'admin';
//         $email = 'admin@example.com';
//         $password = $passwordEncoder->encodePassword($user, 'password');
//         $firstName = 'Francisco';
//         $lastName = 'Bueno';
//
//         $user->setUsername($username);
//         $user->setEmail($email);
//         $user->setPassword($password);
//         $user->setFirstName($firstName);
//         $user->setLastName($lastName);
//         $user->setEnabled(true);
//         $em = $this->getDoctrine()->getManager();
//         $em->persist($user);
//         $em->flush();
    }
}
