<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle('A first Fixture Post');
        $blogPost->setPublished("2020-02-28 10:10:10");
        $blogPost->setContent("You've already seen how the repository object allows you to run basic queries without any work");
        $blogPost->setAuthor('Hercival Aragon');
        $blogPost->setSlug('a-first-fixture-post');

        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('A second Fixture Post');
        $blogPost->setPublished("2020-03-01 10:11:59");
        $blogPost->setContent("Already allows you to run basic queries without any work");
        $blogPost->setAuthor('Hercival Aragon');
        $blogPost->setSlug('a-second-fixture-post');
        $manager->persist($blogPost);
        $manager->flush();
    }
}
