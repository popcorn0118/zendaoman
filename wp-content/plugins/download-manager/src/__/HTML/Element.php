<?php

namespace WPDM\__\HTML;

class Element
{
	protected string $tag;
	protected array $attributes = [];
	protected string $content = '';
	protected bool $selfClosing;

	public function __construct(string $tag, bool $selfClosing = false)
	{
		$this->tag = $tag;
		$this->selfClosing = $selfClosing;
	}

	public function attr(string $key, string $value): self
	{
		$this->attributes[$key] = $value;
		return $this;
	}

	public function attrs(array $attributes): self
	{
		foreach ($attributes as $key => $value) {
			$this->attr($key, $value);
		}
		return $this;
	}

	public function data(string $key, string $value): self
	{
		return $this->attr("data-{$key}", $value);
	}

	public function content(string $content): self
	{
		$this->content = $content;
		return $this;
	}

	public function render(): string
	{
		$attrString = '';
		foreach ($this->attributes as $key => $value) {
			$attrString .= " {$key}='" . htmlspecialchars($value, ENT_QUOTES) . "'";
		}

		if ($this->selfClosing) {
			return "<{$this->tag}{$attrString} />";
		}

		return "<{$this->tag}{$attrString}>{$this->content}</{$this->tag}>";
	}

	public function __toString(): string
	{
		return $this->render();
	}
}
