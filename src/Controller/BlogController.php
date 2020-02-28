<?php
namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_list", defaults={"page": 5}, requirements={"page"="\d+"})
     */
    public function list($page, Request $request)
    {
        $limit = $request->get('limit', 10);
        $repository =  $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();
        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function (BlogPost $item){
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug() ]);
                }, $items)
            ]    
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("singlePost", class="App:BlogPost")
     */
    public function post($singlePost)
    {
        // return $this->json(
        //     $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
        // );
        return $this->json($singlePost);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @ParamConverter("singlePost", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
     */
    public function postBySlug($singlePost)
    {
        // return $this->json(
        //     $this->getDoctrine()->getRepository(BlogPost::class)->findBy(['slug' => $slug])
        // );
        return $this->json($singlePost);
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     */

    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $blogPost =  $serializer->deserialize($request->getContent(), BlogPost::class, 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);

    }

    /**
     * @Route("/delete/{id}", name="blog_delete", methods={"DELETE"})
     */
    public function delete($id)
    {
        $post = $this->getDoctrine()->getRepository(BlogPost::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

}