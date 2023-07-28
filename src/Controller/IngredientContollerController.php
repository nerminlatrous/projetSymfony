<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientsType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class IngredientContollerController extends AbstractController
{
    #[Route('/ingredient/contoller', name: 'app_ingredient_contoller', methods: ['GET'])]    
    /**
     * index
     *this funtion display all ingredients
     * @param  mixed $repository
     * @param  mixed $paginator
     * @param  mixed $request
     * @return Response
     */
    public function index(IngredientRepository $repository ,PaginatorInterface $paginator,Request $request): Response
    {   

        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        
        return $this->render('pages/ingredient_contoller/index.html.twig', [
            'ingredients'=>$ingredients
        ]);
    }
    
    #[Route('/ingredient/nouveau',name: 'ingredient.new' , methods: ['GET' , 'POST'])]
    public function new(Request $request,
    EntityManagerInterface $manager
    ): Response {

        $ingredient = new Ingredient();
        $form=$this->createForm(IngredientsType::class, $ingredient);

        $form->handleRequest($request);
        
        if($form ->isSubmitted() && $form->isValid()){
               $ingredient =$form->getData();
                $manager->persist($ingredient);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'votre ingrédient a été créé avec succés !'
                );

              return  $this->redirectToRoute('app_ingredient_contoller');
               
        }
        return $this->render('pages/ingredient_contoller/new.html.twig', [
            'form'=>$form->createView()
        ]);
    }
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET','POST'])]
    public function edit( Request $request, EntityManagerInterface $manager,$id): Response
    {
        $ingredient = $manager->getRepository(Ingredient::class)->find($id);
        $form = $this->createForm(IngredientsType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingredient a été modifié avec succées'
            );
            return $this->redirectToRoute('app_ingredient_contoller');
        }
        return $this->render('pages/ingredient_contoller/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }
    #[Route('/ingredient/suppression/{id}', name:'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient,$id):Response 
    {

        $ingredient = $manager->getRepository(Ingredient::class)->find($id);
        if(!$ingredient){
            $this->addFlash(
                'success',
                'l\'ingrédient en question n\'a pas été trouvé !'
            );
            return $this->redirectToRoute('app_ingredient_contoller');
        }
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient a été supprimé avec succés !'
        );
        return $this->redirectToRoute('app_ingredient_contoller');

    }
}