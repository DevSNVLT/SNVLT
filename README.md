# BIENVENUE SUR SNVLT

**SNVLT** est une plateforme collaborative en relation avec l'activité forestière en Côte d'Ivoire. IL inclut différents types d'acteurs à savoir :  

		les exploitants forestiers  
		les transformateurs de bois
		les exportateurs
		les observateurs indépendants
		l'administration forestière y compris tous les services décentralisés
		le grand public*


# Préréquis
	- Version PHP >= 8.2  
	- postgresql >= 9.3  
	- composer  
	- Client symfony >= 5.8.3  
	- Symfony >= 6.2


## Installation

	- Técharger votre application SNVLT dans le dossier de votre choix  
	- Ouvrir votre application dans l'éditeur de votre choix (VSCODE, PHP STORM, INTELLIJ, SUBLIME TEXT, NOTEPAD, etc.)
	
	- Dans la console (e. invite de commande CMD), installer les dépendances via composer : composer install
	
	- Installer les bases de données SNVLT_1 et SNVLT_2 présentes dans le dossier BD. NB : Dézipper la base de données SNVLT_2  
	Astuce : Pour installer les bases de données, copiez les BD sur la racine du lecteur C ou D puis utilisez la commande \i C:/{nom_de_la_BD} dans psql.
	
	- Configurer le fichier env.local.php de votre projet en reférençant les bases DATABASE_URL et DATABASE_URL_ALT.  
	Ex. : 'DATABASE_URL' => 'postgresql://postgres:020780@127.0.0.1:5432/snvlt2'  
	      'DATABASE_URL_ALT' => 'postgresql://postgres:020780@127.0.0.1:5433/snvlt1'
	  
	- Daans l'invite de commande CMD (console dev), tapez la commande symfony serve.

