<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    //READ ALL BOOKS
    #[Route('/api/books', name: 'books', methods:['GET'])]
    public function getBookList(BookRepository $bookRepository, 
    SerializerInterface $serializer): JsonResponse
    {
        //Get books from database
        $bookList = $bookRepository->findAll(); 
        $jsonBookList  = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']); 
        
        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/books/{id}', name: 'detailBook', methods: ['GET'])]
    public function detailBook(Book $book, SerializerInterface $serializer):JsonResponse
    {
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']); 
        return new JsonResponse($jsonBook, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    //DELETE
    #[Route('/api/books/{id}', name: 'deleteBook', methods: ['DELETE'])]
    public function deleteBook(Book $book, EntityManagerInterface $em) 
    {
        $em->remove($book);
        $em->flush(); 

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/books', name: 'createBook', methods:['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGeneratorInterface, AuthorRepository $authorRepository )
    {
        $book = $serializer->deserialize($request->getContent(), Book::class,'json');

        //Get datas as array 
        $content = $request->toArray();

        //Get user id from data 
        $idAuthor = $content['authorId'] ?? -1 ;

        //Get the matching author 
        $book->setAuthor($authorRepository->find( $idAuthor ));

        $entityManager->persist($book); 
        $entityManager->flush(); 

        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        
        $location = $urlGeneratorInterface->generate('detailBook', ['id' => $book->getId(), UrlGeneratorInterface::ABSOLUTE_URL]  );

        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ['Location'=> $location], true);
    }

    
    #[Route('/api/books/{id}', name: 'updateBook', methods: ['PUT'])]
    public function updateBook(Request $request, SerializerInterface $serializerInterface, 
    Book $currentbook, EntityManagerInterface $em, AuthorRepository $authorRepository): JsonResponse
    {
        $updatedBook = $serializerInterface->deserialize($request->getContent(), 
            Book::class, 
            'json', 
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentbook ]
        );
        
        $content = $request->toArray();
        $idAuthor = $content['idAuthor'] ?? -1 ;
        $updatedBook->setAuthor( $authorRepository->find( $idAuthor ));

        $em->persist( $updatedBook );
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    
}
