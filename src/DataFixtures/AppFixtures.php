<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Picture;
use App\Entity\Video;
use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Comment;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function Symfony\Component\String\u;


class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadCategories($manager);
        $this->loadPictures($manager);
        $this->loadUsers($manager);
        $this->loadVideos($manager);
        $this->loadTricks($manager);
    }

    private function loadCategories(ObjectManager $manager): void
    {
        foreach ($this->getCategoryData() as [$ref, $name]) {
            $category = new Category();
            $category->setName($name);

            $manager->persist($category);
            $this->addReference('cat-'.$ref, $category);
        }

        $manager->flush();
    }

    private function getCategoryData(): array
    {
        return [
            ['1', 'Grab'],
            ['2', 'Slide'],
            ['3', 'Flip'],
            ['4', 'Old School']
        ];
    }

    private function loadPictures(ObjectManager $manager): void
    {
        foreach ($this->getPictureData() as [$ref, $source, $alternateText]) {
            $picture = new Picture();
            $picture->setSource($source);
            $picture->setAlternateText($alternateText);

            $manager->persist($picture);
            $this->addReference('pic-'.$ref, $picture);
        }

        $manager->flush();
    }

    private function getPictureData(): array
    {
        return [
            ['1', 'demo/backflip1-a17f14a3ee1963f2c5552b11318c207c.jpg', 'image backflip1'],
            ['2', 'demo/backflip2-58ab9e8778a3b23d3af184f03794682d.jpg', 'image backflip2'],
            ['3', 'demo/backflip3-36d2bd71537b95a1bc515374b1959807.jpg', 'image backflip3'],
            ['4', 'demo/backsideair1-32490f442b23ebe57568f527cd16175e.png', 'image backsideair1'],
            ['5', 'demo/backsideair2-d78a4bf0a20efd9c900297357b3d9c6a.jpg', 'image backsideair2'],
            ['6', 'demo/backsideair3-75cf7313159492b57ce57ee58de22dc8.jpg', 'image backsideair3'],
            ['7', 'demo/frontflip1-7fc1478a5ec5e07fdbcd49501bb23d12.jpg', 'image frontflip1'],
            ['8', 'demo/frontflip2-7ed74bf0aa313ce756cd7b8d45ad60ac.jpg', 'image frontflip2'],
            ['9', 'demo/frontflip3-3302de2bd777f37089ea813d9b4329d6.jpg', 'image frontflip3'],
            ['10', 'demo/indy1-498edff9eeae81679afb2f26bd522386.jpg', 'image indy1'],
            ['11', 'demo/indy2-00a76f91eea4ef5b967585189bac1e17.jpg', 'image indy2'],
            ['12', 'demo/indy3-efbdd8bd5dc98ed677d02b037de16ac4.jpg', 'image indy3'],
            ['13', 'demo/japan1-11d32ba88a55d0d042ba6355fecc0076.jpg', 'image japan1'],
            ['14', 'demo/japan2-2c7d404949faa6dc53286e3bff93da0c.jpg', 'image japan2'],
            ['15', 'demo/japan3-e900264ff6aef11bf70e861cf54dba55.jpg', 'image japan3'],
            ['16', 'demo/methodair1-1a4f77d278b1f78d920eecce944ca3dd.jpg', 'image methodair1'],
            ['17', 'demo/methodair2-f91d7140dcb8ceeeef929883d14e3231.jpg', 'image methodair2'],
            ['18', 'demo/methodair3-9442c2fb13cc61029684ccd20c5e13a4.png', 'image methodair3'],
            ['19', 'demo/mute1-7421b4ef48cfb72222c1f52bad22dbbf.jpg', 'image mute1'],
            ['20', 'demo/mute2-891924453d904f9f13e43e116c1256c7.jpg', 'image mute2'],
            ['21', 'demo/mute3-ecc803fe240804e8fa0f13c69a7e56e5.jpg', 'image mute3'],
            ['22', 'demo/sad1-7086d00d2b88fa386486df103b064fa8.jpg', 'image sad1'],
            ['23', 'demo/sad2-3b917e3225fa4ed25584d9529c8f0e46.jpg', 'image sad2'],
            ['24', 'demo/stailefish1-189e7421b31eb1368e466379b188284b.jpg', 'image stailefish1'],
            ['25', 'demo/stailefish2-f844256f41b13fd3d5e0084205cb57b5.jpg', 'image stailefish2'],
            ['26', 'demo/methodair3-5b4e7f2969569006e7804ebd50b28600.png', 'image avatar'],
            ['27', 'demo/japan2-2789078a496bbaafc3e77749aeb3e7a0.jpg', 'image avatar']
        ];
    }

    private function loadVideos(ObjectManager $manager): void
    {
        foreach ($this->getVideoData() as [$ref, $source]) {
            $video = new Video();
            $video->setSource($source);

            $manager->persist($video);
            $this->addReference('vid-'.$ref, $video);
        }
    }

    private function getVideoData(): array
    {
        return [
            ['1', 'https://youtube.com/embed/CzDjM7h_Fwo'],
            ['2', 'https://youtube.com/embed/I7N45iRPrhw'],
            ['3', 'https://youtube.com/embed/_CN_yyEn78M'],
            ['4', 'https://youtube.com/embed/-7uZQQWiEak'],
            ['5', 'https://youtube.com/embed/AMsWP9WJS_0'],
            ['6', 'https://youtube.com/embed/SlhGVnFPTDE'],
            ['7', 'https://youtube.com/embed/jm19nEvmZgM'],
            ['8', 'https://youtube.com/embed/k6aOWf0LDcQ'],
            ['9', 'https://youtube.com/embed/KEdFwJ4SWq4'],
            ['10', 'https://youtube.com/embed/IGy2hEIIKs0'],
            ['11', 'https://youtube.com/embed/6yA3XqjTh_w'],
            ['12', 'https://youtube.com/embed/RmqxKCUZS7s'],
            ['13', 'https://youtube.com/embed/f9FjhCt_w2U'],
            ['14', 'https://youtube.com/embed/6z6KBAbM0MY'],
            ['15', 'https://youtube.com/embed/hUddT6FGCws'],
            ['16', 'https://youtube.com/embed/hUQ3eKS13co'],
            ['17', 'https://youtube.com/embed/GS9MMT_bNn8'],
            ['18', 'https://youtube.com/embed/eGJ8keB1-JM'],
            ['19', 'https://youtube.com/embed/xhvqu2XBvI0'],
            ['20', 'https://youtube.com/embed/gevwK7GxZAQ'],
            ['21', 'https://youtube.com/embed/_hxLS2ErMiY'],
            ['22', 'https://youtube.com/embed/hzilQIqW6J8']
        ];
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$ref, $username, $password, $email, $roles, $avatar]) {
            $user = (new User())
                ->setUsername($username)
                ->setEmail($email)
                ->setRoles($roles)
                ->setIsVerified('1')
                ->setCreatedAt(new \DateTimeImmutable('now - '.$ref.'days'));

            if ($avatar) {
                $user->setAvatar($this->getReference('pic-'.$avatar));
            }

            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            
            $manager->persist($user);
            $this->addReference('usr-'.$ref, $user);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            ['1', 'admin', 'azerty', 'admin@snowtricks.com', ['ROLE_ADMIN'], null],
            ['2', 'Tom Doe', 'azerty', 'tom.doe@snowtricks.com', ['ROLE_VERIFIED_USER'], 26],
            ['3', 'John Doe', 'azerty', 'john.doe@snowtricks.com', ['ROLE_VERIFIED_USER'], 27],
            ['4', 'Jane Doe', 'azerty', 'jane.doe@snowtricks.com', ['ROLE_VERIFIED_USER'], null],
        ];
    }

    private function loadTricks(ObjectManager $manager): void
    {
        foreach ($this->getTrickData() as $trickIndex => [$title, $updatedAt, $category, $pictures, $mainPicture, $videos]) {
            $trickIndex ++;
            $trick = (new Trick())
                ->setTitle($title)
                ->setDescription($this->getRandomText(random_int(255, 512)))
                ->setCreatedAt(new \DateTimeImmutable('now - '.$trickIndex.'days'))
                ->setUpdatedAt($updatedAt ? new \DateTimeImmutable('now - '.$updatedAt.'hours') : null)
                ->setAuthor($this->getReference('usr-'.$this->getRandomUser()))
                ->setCategory($this->getReference('cat-'.$category));

                foreach ($pictures as $picture) {
                    $trick->addPicture($this->getReference('pic-'.$picture));
                }

                if ($mainPicture) {
                    $trick->setMainPicture($this->getReference('pic-'.$mainPicture));
                }

                foreach ($videos as $video) {
                    $trick->addVideo($this->getReference('vid-'.$video));
                }

                foreach (range(0, random_int(1, count($this->getUserData()))) as $userRef) {
                    if ($userRef === 0) {
                        break;
                    }
                    if ($trick->getAuthor() != $this->getReference('usr-'.$userRef) && !$trick->getUsersWhiteList()->contains($this->getReference('usr-'.$userRef))) {
                        $trick->addUsersWhiteList($this->getReference('usr-'.$userRef));
                    }
                }

                foreach (range(0, random_int(1, 15)) as $commentIndex) {
                    $comment = (new Comment())
                        ->setAuthor($this->getReference('usr-'.$this->getRandomUser()))
                        ->setContent($this->getRandomText(random_int(64, 128)))
                        ->setCreatedAt(new \DateTimeImmutable('now - '.$trickIndex.'days + '.$commentIndex.'minutes'));

                    $trick->addComment($comment);
                }

            $manager->persist($trick);
            $this->addReference('trk-'.$trickIndex, $trick);
        }

        $manager->flush();
    }

    private function getTrickData(): array
    {
        // $trickIndex => [$title, $updatedAt, $category, $pictures, $mainPicture, $videos]
        return [
            ['Backside Air', null, 4, [4, 5, 6], 4, [3, 4]],
            ['Japan Air', 1, 4, [13, 14, 15], 15, [1, 2]],
            ['Backflip', 2, 3, [1, 2, 3], null, [5, 6]],
            ['Mute', 1, 1, [19, 20, 21], 20, [7, 8]],
            ['Sad', 3, 1, [22, 23], null, [9, 10]],
            ['Indy', null, 1, [10, 11, 12], 12, [11, 12]],
            ['Stalefish', 3, 1, [24, 25], 25, [13, 14]],
            ['360', 4, 3, [], null, [15, 16 ,17]],
            ['Frontflip', 5, 3, [7, 8, 9], null, [18, 19, 20]],
            ['Method Air', 1, '4', [16, 17, 18], 16, [21, 22]],
            ['One more trick', 1, $this->getRandomCategory(), [], null, []],
            ['More trick one', 1, $this->getRandomCategory(), [], null, []],
            ['Trick one more', 1, $this->getRandomCategory(), [], null, []],
            ['One trick more', 1, $this->getRandomCategory(), [], null, []],
            ['Trick more one', 1, $this->getRandomCategory(), [], null, []],
            ['More one trick', 1, $this->getRandomCategory(), [], null, []],
            ['One more trick 2', 1, $this->getRandomCategory(), [], null, []],
            ['More trick one 2', 1, $this->getRandomCategory(), [], null, []],
            ['Trick one more 2', 1, $this->getRandomCategory(), [], null, []],
            ['One trick more 2', 1, $this->getRandomCategory(), [], null, []],
            ['Trick more one 2', 1, $this->getRandomCategory(), [], null, []],
            ['More one trick 2', 1, $this->getRandomCategory(), [], null, []]
        ];
    }

    private function getRandomUser()
    {
        return random_int(1, count($this->getUserData()));
    }

    private function getRandomCategory()
    {
        return random_int(1, count($this->getCategoryData()));
    }

    private function getRandomText(int $maxLength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);

        do {
            $text = u('. ')->join($phrases)->append('!');
            array_pop($phrases);
        } while ($text->length() > $maxLength);

        return $text;
    }

    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
            'Dictum non consectetur',
        ];
    }

}
