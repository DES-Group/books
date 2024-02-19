<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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

    #[Route('/api/authors/{id}', name:'author', methods:['GET'])]
    public function getBookDetail(Author $author, SerializerInterface $serializer)
    {  
        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => 'getAuthors'] );

        return new JsonResponse($jsonAuthor, Response::HTTP_OK, ['accept'=> 'json'], true);
    }

    #[Route('/api/author/{id}', name:'deleteAuthor', methods: ['DELETE'])]
    public function deleteAuthor(Author $author, EntityManagerInterface $em)
    {
        $em->remove($author); 
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/book/{id}', name:'insertAuthor', methods:['POST'])]
    public function insertAuthor()
    {
        
    }
}
