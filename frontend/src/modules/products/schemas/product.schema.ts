import { toTypedSchema } from '@vee-validate/zod'
import { z } from 'zod'
import { MAX_VALUE } from '@/shared/utils/currency'

export const productSchema = toTypedSchema(
  z.object({
    name: z
      .string()
      .min(1, 'Informe o nome do produto.')
      .max(255, 'O nome deve ter no máximo 255 caracteres.'),
    description: z
      .string()
      .max(2000, 'A descrição deve ter no máximo 2000 caracteres.')
      .optional()
      .default(''),
    price: z.coerce
      .number({ invalid_type_error: 'Informe um preço válido maior que zero.' })
      .min(0.01, 'Informe um preço válido maior que zero.')
      .max(MAX_VALUE, 'O preço máximo permitido é 99.999.999,99.'),
    stock_quantity: z.coerce
      .number({ invalid_type_error: 'Informe um estoque inicial valido.' })
      .int('O estoque deve ser um numero inteiro.')
      .min(0, 'Informe um estoque inicial valido.'),
  }),
)
