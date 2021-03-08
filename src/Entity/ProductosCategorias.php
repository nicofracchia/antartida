<?php

namespace App\Entity;

use App\Repository\ProductosCategoriasRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductosCategoriasRepository::class)
 */
class ProductosCategorias
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Productos::class, inversedBy="productosCategorias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $producto;

    /**
     * @ORM\ManyToOne(targetEntity=Categorias::class, inversedBy="productosCategorias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoria;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProducto(): ?Productos
    {
        return $this->producto;
    }

    public function setProducto(?Productos $producto): self
    {
        $this->producto = $producto;

        return $this;
    }

    public function getCategoria(): ?Categorias
    {
        return $this->categoria;
    }

    public function setCategoria(?Categorias $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }
}
