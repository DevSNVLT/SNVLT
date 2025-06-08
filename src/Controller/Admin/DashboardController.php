<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Blog\ArticleBlogCrudController;
use App\Controller\Admin\Securite\OptionCrudController;
use App\Controller\Admin\ContacterCrudController;
use App\Entity\Admin\Option;
use App\Entity\Autres\Contacter;
use App\Entity\Blog\ArticleBlog;
use App\Entity\Blog\AutresRubriques;
use App\Entity\Blog\Categorie;
use App\Entity\Blog\CategoryPublication;
use App\Entity\Blog\Comment;
use App\Entity\Blog\GroupePublication;
use App\Entity\Blog\Media;
use App\Entity\Blog\Publication;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private  AdminUrlGenerator $adminUrlGenerator)
    {

    }

    #[Route('/admin-snvlt', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setController(ArticleBlogCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SNVLT ADMIN');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUserIdentifier())
            ->setGravatarEmail($user->getEmail())
            ->setAvatarUrl('https://127.0.0.1:8000/images/uploads/users/' . $user->getPhoto())
            ->displayUserAvatar(true);

    }
    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('public/assets/css/admin.css');
    }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::subMenu('Actualités / Blog', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Tous les articles','fas fa-newspaper', ArticleBlog::class),
            MenuItem::linkToCrud('Catégories','fas fa-list',Categorie::class),
            MenuItem::linkToCrud('Commentaires','fas fa-comment',Comment::class),
            MenuItem::linkToCrud('Media','fas fa-photo-video',Media::class)
        ]);
        /*yield MenuItem::linkToCrud('Suggestions','fas fa-message', Contacter::class);*/
        yield MenuItem::subMenu('Informations publiques', 'fas fa-newspaper')->setSubItems([
            MenuItem::linkToCrud('Groupes','fas fa-newspaper', GroupePublication::class),
            MenuItem::linkToCrud('Catégories','fas fa-list',CategoryPublication::class),
            MenuItem::linkToCrud('Publica&tions','fas fa-comment',Publication::class)
        ]);
        yield MenuItem::subMenu('Autres rubriques', 'fas fa-tools')->setSubItems([
            MenuItem::linkToCrud('Infos sur le ministre','fas fa-tools',AutresRubriques::class)
        ]);

        yield MenuItem::subMenu('Confriguration', 'fas fa-tools')->setSubItems([
            MenuItem::linkToCrud('Options','fas fa-cog',Option::class)
        ]);
    }
}
