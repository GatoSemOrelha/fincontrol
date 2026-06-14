from PIL import Image

# Abre a imagem e força o formato RGBA limpo (removendo paletas indexadas ou chunks bKGD)
img = Image.open("public/fincontrol.png").convert("RGBA")

# Cria uma nova imagem limpa
clean_img = Image.new("RGBA", img.size)
clean_img.paste(img, (0, 0), img)

# Salva sobrescrevendo
clean_img.save("public/fincontrol.png", "PNG")
print("Imagem limpa e re-salva com sucesso!")
