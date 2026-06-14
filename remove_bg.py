from PIL import Image
import numpy as np

img = Image.open("public/fincontrol.png").convert("RGBA")
data = np.array(img)

# Detect pure black background or very dark background
r, g, b, a = data[:, :, 0], data[:, :, 1], data[:, :, 2], data[:, :, 3]

# threshold for black
mask_black = (r < 20) & (g < 20) & (b < 20)

# Apply transparency
data[mask_black, 3] = 0

# Anti-aliasing for edges
mask_edge = (r >= 20) & (r < 60) & (g >= 20) & (g < 60) & (b >= 20) & (b < 60)
data[mask_edge, 3] = 128

result = Image.fromarray(data)
result.save("public/fincontrol.png", "PNG")
print("Remoção de fundo aplicada com sucesso!")
