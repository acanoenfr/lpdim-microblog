<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog_index")
     *
     * @param PostRepository $repo
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PostRepository $repo, Request $request, ObjectManager $manager)
    {
        $post = new Post();
        $formPost = $this->createForm(PostType::class, $post);
        $posts = $repo->findAll();
        $formPost->handleRequest($request);
        if ($formPost->isSubmitted() && $formPost->isValid()) {
            $post->setAuthor($this->getUser());
            $post->setCreatedAt(new \DateTime());
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('blog_index');
        }
        return $this->render('micro_blog/index.html.twig', [
            'form' => $formPost->createView(),
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/edit/{id}", name="blog_edit")
     *
     * @param Post $post
     * @param Request $request
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Post $post, Request $request, ObjectManager $manager)
    {
        $formEdit = $this->createForm(PostType::class, $post);
        $formEdit->handleRequest($request);
        if ($formEdit->isSubmitted() && $formEdit->isValid()) {
            $manager->persist($post);
            $manager->flush();
            return $this->redirectToRoute('blog_index');
        }
        return $this->render('micro_blog/edit.html.twig', [
            'form' => $formEdit->createView(),
            'post' => $post
        ]);
    }

    /**
     * @Route("/delete/{id}", name="blog_delete")
     *
     * @param Post $post
     * @param ObjectManager $manager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Post $post, ObjectManager $manager)
    {
        if (isset($_POST["submit"])) {
            $manager->remove($post);
            $manager->flush();
            return $this->redirectToRoute('blog_index');
        }
        return $this->render('micro_blog/delete.html.twig', [
            'post' => $post
        ]);
    }
}
