<div id="top"></div>
<!--
*** Thanks for checking out the Bind-it Manager. If you have a suggestion
*** that would make this better, please fork the repo and create a pull request
*** or simply open an issue with the tag "enhancement".
*** Don't forget to give the project a star!
*** Thanks again! Now go create something AMAZING! :D
-->

<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->

[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]

<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/hakrichTech/bindIt">
    <img src="images/logo.png" alt="Logo" width="80" height="80">
  </a>

  <h3 align="center">PHPShots/bint-it [Dependecies Injection Manager]</h3>

  <p align="center">
    A common library for managing Dependecies Injection in PHP.
    <br />
    <a href="https://github.com/hakrichTech/bindIt"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://github.com/hakrichTech/bindIt">View Demo</a>
    ·
    <a href="https://github.com/hakrichTech/bindIt/issues">Report Bug</a>
    ·
    <a href="https://github.com/hakrichTech/bindIt/issues">Request Feature</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>

# PHPShots Bind-It

`phpshots/bind-it` is a powerful and lightweight PHP container library that simplifies dependency injection and contextual bindings. It provides intuitive methods to manage singleton instances, contextual bindings, and dependency resolution in your PHP applications.

---

## Why Use Bind-It?

- **Flexibility and Control**: Define how your application resolves dependencies with complete control over bindings, contextual mappings, and singleton instances.
- **Simplicity**: Clean and intuitive API for managing dependencies, allowing developers to focus on application logic.

- **Contextual Binding**: Bind different implementations of a dependency based on the requesting class or context.

- **Scalability**: Suitable for both small applications and large-scale systems, adapting seamlessly to growth.

- **Integration-Friendly**: Lightweight and easy to integrate into any PHP project or framework.

- **Testing Made Easy**: Improves testability by decoupling dependencies, making integration with PHPUnit or other testing frameworks seamless.

---

## Features

- **Bind and Resolve**: Easily bind classes or interfaces to implementations and resolve them when needed.
- **Singletons**: Manage single instances across the application lifecycle.
- **Contextual Binding**: Bind different implementations of a dependency based on the requesting class or context.
- **Automatic Resolution**: Automatically resolve class dependencies using reflection.
- **Flexible Configuration**: Adapt bindings for varying application needs.

---

<!-- GETTING STARTED -->

## Getting Started

Follow these steps to set up and start using the Bind-it library in your PHP project.

### Prerequisites

Before you begin, ensure you have the following installed:

- PHP (version 8.2 or higher)
  ```sh
  apt install php
  ```
- Composer (for dependency management)
  ```sh
  apt install composer
  ```

### Installation

1. **Clone the Repository**: Start by cloning the repository to your local machine:

   ```bash
   git clone https://github.com/hakrichTech/bindIt.git
   ```

2. Navigate to the Project Directory
   ```sh
   cd bindIt
   ```
3. **Install Dependencies**: Use Composer to install the required dependencies:
   ```sh
   composer install
   ```
4. **Or install the package using composer**:
   ```bash
    composer require phpshots/bind-it
   ```

## Basic Setup

1. **Include the Library**: In your PHP script, include the Composer autoload file to access the Bind-it library:

   ```php
   require 'vendor/autoload.php'; // Adjust the path as necessary
   ```

2. **Binding and Resolving Services**: Bind a class or interface to a concrete implementation:

   ```php
   use PHPShots\Common\Container;

   $container = new Container();

   $container->bind('ConditionalService', function ($container) {
       return new ConditionalService();
   });

   $service = $container->make('ConditionalService');
   ```

3. **Singleton Binding**: Ensure only one instance of a class is shared across the application:

   ```php
   $container->singleton('GreetingService', GreetingService::class);

   $instance1 = $container->make('GreetingService');
   $instance2 = $container->make('GreetingService');

   if($instance1 === $instance2):; // Both instances are the same
   endif;
   ```

4. **Contextual Binding**: Provide different implementations of a dependency based on the requesting class:

    ```php
    use PHPShots\Common\ContextualBindingBuilder;

    $contextualBinding = new ContextualBindingBuilder($container, ConcreteClass::class);
    $contextualBinding->needs(AbstractClass::class)->give(ConcreteImplementation::class);

    $instance = $container->make(ConcreteClass::class);
    if($instance->getDependency() instanceof ConcreteImplementation):
    {
      echo true;
    }
    ```

    Or use shorthand:

    ```php
    $container->addContextualBinding(ConcreteClass::class, AbstractClass::class, ConcreteImplementation::class);

    ```

5. **Resolving with Dependencies**: Automatically resolve dependencies for complex class hierarchies:

      ```php
      $container->bind(AbstractClass::class, ConcreteImplementation::class);
      $container->bind(ConcreteClass::class, ClassWithDependency::class);

      $resolved = $container->make(ConcreteClass::class);
      if($resolved instanceof ClassWithDependency){
        echo true;
      }
      if($resolved->getDependency() instanceof ConcreteImplementation){
        echo true;
      }

      ```

<!-- CONTRIBUTING -->

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<!-- LICENSE -->

## License

Distributed under the MIT License. See `LICENSE.txt` for more information.

<!-- CONTACT -->

## Contact

Shamavu Rasheed - [@hakeem-shamavu](www.linkedin.com/in/hakeem-shamavu) - shamavurasheed@gmail.com

Project Link: [https://github.com/hakrichTech/bindIt](https://github.com/hakrichTech/bindIt)

<!-- ACKNOWLEDGMENTS -->

## Acknowledgments

Use this space to list resources you find helpful and would like to give credit to. I've included a few of my favorites to kick things off!

- [Choose an Open Source License](https://choosealicense.com)
- [GitHub Emoji Cheat Sheet](https://www.webpagefx.com/tools/emoji-cheat-sheet)
- [Malven's Flexbox Cheatsheet](https://flexbox.malven.co/)
- [Malven's Grid Cheatsheet](https://grid.malven.co/)
- [Img Shields](https://shields.io)
- [GitHub Pages](https://pages.github.com)
- [Font Awesome](https://fontawesome.com)
- [React Icons](https://react-icons.github.io/react-icons/search)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->

[contributors-shield]: https://img.shields.io/github/contributors/othneildrew/Best-README-Template.svg?style=for-the-badge
[contributors-url]: https://github.com/hakrichTech/bindIt/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/othneildrew/Best-README-Template.svg?style=for-the-badge
[forks-url]: https://github.com/hakrichTech/bindIt/network/members
[stars-shield]: https://img.shields.io/github/stars/othneildrew/Best-README-Template.svg?style=for-the-badge
[stars-url]: https://github.com/hakrichTech/bindIt/stargazers
[issues-shield]: https://img.shields.io/github/issues/othneildrew/Best-README-Template.svg?style=for-the-badge
[issues-url]: https://github.com/hakrichTech/bindIt/issues
[license-shield]: https://img.shields.io/github/license/othneildrew/Best-README-Template.svg?style=for-the-badge
[license-url]: https://github.com/hakrichTech/bindIt/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/hakeem-shamavu
[product-screenshot]: images/screenshot.png
