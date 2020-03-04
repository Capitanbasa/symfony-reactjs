<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker =  \Faker\Factory::create();

    }
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPost($manager);
        $this->loadComments($manager);
        
    }

    public function loadBlogPost(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');

        for($x= 0; $x < 20 ; $x++){
            $string_title = $this->faker->realText(30);
            $blogPost = new BlogPost();
            $blogPost->setTitle($string_title);
            $blogPost->setPublished($this->faker->dateTimeThisYear());
            $blogPost->setContent($this->faker->realText());
            $blogPost->setAuthor($user);
            $blogPost->setSlug($this->slugGenerator($string_title));
            $this->setReference("blog_post_$x", $blogPost);
            $manager->persist($blogPost);
        }
        $manager->flush();

    }

    public function loadComments(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');
        for($x= 0; $x < 20 ; $x++){
            for($c= 0; $c < rand(1,10) ; $c++){
                $blogpost = $this->getReference("blog_post_$x");
                $comment = new Comment();
                $comment->setContent($this->faker->realText(50));
                $comment->setPublished($this->faker->dateTimeThisYear());
                $comment->setBlogPost($blogpost);
                $comment->setAuthor($user);
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'admin'));
        $user->setName('Hercival Aragon');
        $user->setEmail('hercivalaragon@gmail.com');
        $this->addReference('user_admin', $user);
        $manager->persist($user);
        $manager->flush();

    }
    private function slugGenerator($string) {
        $string = strtolower($string);
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
     
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
     }
}
