from PIL import Image
import numpy as np

img = Image.open("public/fincontrol.png").convert("RGBA")
data = np.array(img)
a = data[:, :, 3]
transparent_pixels = np.sum(a == 0)
total_pixels = data.shape[0] * data.shape[1]
print(f"Original image transparent pixels: {transparent_pixels} out of {total_pixels} ({(transparent_pixels/total_pixels)*100:.2f}%)")
