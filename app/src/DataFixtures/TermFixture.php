<?php

namespace App\DataFixtures;

use App\Entity\Definition;
use App\Entity\Term;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class TermFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $term = new Term();
        $term->setValue('test_fixture_term1');

        $def1 = new Definition();
        $def1->setValue('test_fixture_term1_def1');
        $manager->persist($def1);
        $def2 = new Definition();
        $def2->setValue('test_fixture_term1_def2');
        $manager->persist($def2);

        $term->setDefinitions(new ArrayCollection([$def1,$def2]));
        $manager->persist($term);
        $manager->flush();
    }
}
