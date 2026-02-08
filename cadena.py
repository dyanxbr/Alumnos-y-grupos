import time

contador_global = 0

inicio = time.time()

contador_local = contador_global

for i in range(100_000_000):
    contador_local += 1

contador_global = contador_local

fin = time.time()

print("Resultado:", contador_global)
print("Tiempo con variable local:", fin - inicio, "segundos")
