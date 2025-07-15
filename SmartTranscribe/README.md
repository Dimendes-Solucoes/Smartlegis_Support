# 📄 API de Transcrições e Resumos

---

## ✏️ Endpoints de Transcrições

| **Método** | **Rota**                       | **Descrição**                           |
|:----------:|:-------------------------------|:----------------------------------------|
| `GET`      | `/api/transcriptions`          | Lista todas as transcrições realizadas. |
| `POST`     | `/api/transcriptions`          | Realiza a transcrição de um arquivo.    |
| `GET`      | `/api/transcriptions/find`     | Procura uma transcrição específica.     |
| `DELETE`   | `/api/transcriptions`          | Deleta uma transcrição específica.      |

---

## 📚 Endpoints de Resumos

| **Método** | **Rota**                       | **Descrição**                           |
|:----------:|:-------------------------------|:----------------------------------------|
| `GET`      | `/api/resumes`                 | Lista todos os resumos realizados.      |
| `POST`     | `/api/resumes`                 | Cria um novo resumo.                    |
| `DELETE`   | `/api/resumes/:id`             | Deleta um resumo específico.            |