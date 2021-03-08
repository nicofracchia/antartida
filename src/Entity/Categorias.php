<?php

namespace App\Entity;

use App\Repository\CategoriasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoriasRepository::class)
 */
class Categorias
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="integer")
     */
    private $padre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grupo;

    /**
     * @ORM\Column(type="smallint")
     */
    private $habilitado;

    /**
     * @ORM\Column(type="smallint")
     */
    private $eliminado;

    /**
     * @ORM\OneToMany(targetEntity=ProductosCategorias::class, mappedBy="categoria")
     */
    private $productosCategorias;

    public function __construct()
    {
        $this->productosCategorias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getPadre(): ?int
    {
        return $this->padre;
    }

    public function setPadre(int $padre): self
    {
        $this->padre = $padre;

        return $this;
    }

    public function getGrupo(): ?string
    {
        return $this->grupo;
    }

    public function setGrupo(?string $grupo): self
    {
        $this->grupo = $grupo;

        return $this;
    }

    public function getHabilitado(): ?int
    {
        return $this->habilitado;
    }

    public function setHabilitado(int $habilitado): self
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    public function getEliminado(): ?int
    {
        return $this->eliminado;
    }

    public function setEliminado(int $eliminado): self
    {
        $this->eliminado = $eliminado;

        return $this;
    }

    /**
     * @return Collection|ProductosCategorias[]
     */
    public function getProductosCategorias(): Collection
    {
        return $this->productosCategorias;
    }

    public function addProductosCategoria(ProductosCategorias $productosCategoria): self
    {
        if (!$this->productosCategorias->contains($productosCategoria)) {
            $this->productosCategorias[] = $productosCategoria;
            $productosCategoria->setCategoria($this);
        }

        return $this;
    }

    public function removeProductosCategoria(ProductosCategorias $productosCategoria): self
    {
        if ($this->productosCategorias->removeElement($productosCategoria)) {
            // set the owning side to null (unless already changed)
            if ($productosCategoria->getCategoria() === $this) {
                $productosCategoria->setCategoria(null);
            }
        }

        return $this;
    }
}
