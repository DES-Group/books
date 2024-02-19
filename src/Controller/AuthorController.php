<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'authors', methods: ['GET'])]
    public function getAuthors(AuthorRepository $authorRepository, SerializerInterface $serializer): JsonResponse
    {
        $authorList = $authorRepository->findAll();
        $jsonAuthorList = $serializer->serialize($authorList, 'json', ['groups' => 'getAuthors']);  
        
        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/authors/{id}', name:'authorDetail', methods:['GET'])]
    public function getBookDetail(Author $author, SerializerInterface $serializer)
    {  
        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthors'] );

        return new JsonResponse($jsonAuthor, Response::HTTP_OK, ['accept'=> 'json'], true);
    }

    /**
     * Because cascade: ['remove'] is configured on Author entity, when an author is deleted, all associeted books are deleted too.
     */
    #[Route('/api/authors/{id}', name:'deleteAuthor', methods: ['DELETE'])]
    public function deleteAuthor(Author $author, EntityManagerInterface $em)
    {
        $em->remove($author); 
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    #[Route('/api/authors', name:'insertAuthor', methods:['POST'])]
    public function insertAuthor(Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator)
    {
        $author = $serializerInterface->deserialize($request->getContent(), Author::class, 'json' );

        $em->persist($author);
        $em->flush();

        $jsonAuthor = $serializerInterface->serialize($author, 'json', ['groups' => 'getAuthors']);

        $location = $urlGenerator->generate('authorDetail', ['id' => $author->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);

        return new JsonResponse($jsonAuthor, Response::HTTP_CREATED, ['location' => $location], true);
    }

    #[Route('/api/authors/{id}', name: 'updateAuthor', methods: ['PUT'])]
    public function updateAuthor(Author $currentAuthor, Request $request, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $updatedAuthor = $serializer->deserialize($request->getContent(), Author::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAuthor] );

        $em->persist($updatedAuthor);
        $em->flush();


        return new JsonResponse(null, Response::HTTP_NO_CONTENT); 

    }
}
