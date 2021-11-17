<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\EditCategoryFormType;
use App\Form\Handler\CategoryFormHandler;
use App\Form\Model\EditCategoryModel;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/admin/category", name="admin_category_")
 * Class CategoryController
 * @package App\Controller\Admin
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['id' => 'DESC']);

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route ("/edit/{id}", name="edit")
     * @Route ("/add", name="add")
     * @param $id
     */
    public function edit(Request $request, CategoryFormHandler $categoryFormHandler, Category $category = null) // Symfony find product by parameter id
    {
        $category = $category ? : new Category();

        $editCategoryModel = EditCategoryModel::makeFromCategory($category);

        $form = $this->createForm(EditCategoryFormType::class, $editCategoryModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categoryFormHandler->processEditForm($editCategoryModel);

            return $this->redirectToRoute('admin_category_edit', ['id' => $category->getId()]);
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route ("/delete/{id}", name="delete")
     * @param $id
     */
    public function delete(Category $category)
    {

    }
}