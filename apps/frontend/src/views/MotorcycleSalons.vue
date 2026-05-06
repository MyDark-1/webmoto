<template>
  <div class="salons">
    <div class="container">
      <h1 class="salons__title">Мотосалоны</h1>

      <div class="salons__grid">
        <article
          v-for="salon in salons"
          :key="salon.city"
          class="salon-card"
        >
          <div class="salon-card__image">
            <img
              v-if="salon.image"
              :src="salon.image"
              :alt="'Фото салона ' + salon.name"
              class="salon-card__img"
            />
            <div v-else class="salon-card__placeholder">
              <svg
                class="salon-card__icon"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
              </svg>
              <span class="salon-card__placeholder-text">{{ salon.city }}</span>
            </div>
          </div>

          <div class="salon-card__body">
            <h2 class="salon-card__name">{{ salon.name }}</h2>
            <p class="salon-card__city">{{ salon.city }}</p>

            <div class="salon-card__info">
              <div class="salon-card__row">
                <span class="salon-card__label">Адрес:</span>
                <span class="salon-card__value">{{ salon.address }}</span>
              </div>
              <div class="salon-card__row">
                <span class="salon-card__label">Телефон:</span>
                <a
                  :href="`tel:${salon.phone.replace(/[^+\d]/g, '')}`"
                  class="salon-card__value salon-card__value--link"
                >{{ salon.phone }}</a>
              </div>
              <div class="salon-card__row salon-card__row--email">
                <span class="salon-card__label">E-mail:</span>
                <a
                  :href="`mailto:${salon.email}`"
                  class="salon-card__value salon-card__value--link"
                >{{ salon.email }}</a>
              </div>
              <div class="salon-card__row salon-card__row--brands">
                <span class="salon-card__label">Бренды:</span>
                <div class="salon-card__brands">
                  <span
                    v-for="brand in salon.brands"
                    :key="brand"
                    class="salon-card__brand"
                  >{{ brand }}</span>
                </div>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Salon {
  name: string
  city: string
  address: string
  phone: string
  brands: string[]
  email: string
  image?: string
}

const salons: Salon[] = [
  {
    name: 'Radar Extreme Иваново',
    city: 'Иваново',
    address: 'г. Иваново, ул. Пограничника Рыжикова, д. 36',
    phone: '+7(4932) 245-220',
    email: 'yamaha@radar-avto.ru',
    image: 'https://radarextreme.ru/upload/iblock/d08/d0873b5f8f94d001bdf85bed9913016a.jpg',
    brands: [
      'Yamaha', 'Русская механика', 'BRP', 'AODES', 'Stels', 'Bajaj',
      'Honda', 'Tohatsu', 'Kayo', 'Polaris', 'Phoenix', 'Bester',
      'Salut', 'Volzhanka', 'Marlin', 'Hidea', 'M1NSK', 'Gladiator',
      'VOGE', 'Loncin', 'СYCLONE', 'Lifan', 'Segway', 'Zontes',
      'Avantis', 'МЗСА',
    ],
  },
  {
    name: 'Radar Extreme Ярославль',
    city: 'Ярославль',
    address: 'г. Ярославль, ул. Вишняки, д.2',
    phone: '+7 (4852) 63-98-59',
    email: 'radarextreme76@yandex.ru',
    image: 'https://radarextreme.ru/upload/iblock/3ea/eqdv8b0g54l5njwykwdsoh0rs1zkfbbx.jpg',
    brands: [
      'Kayo', 'Marlin', 'Volzhanka', 'Phoenix', 'Hidea',
      'Gladiator', 'Stels', 'Bajaj', 'Salut',
    ],
  },
  {
    name: 'Radar Extreme Кострома',
    city: 'Кострома',
    address: 'г. Кострома, ул. Никитская, д. 70А',
    phone: '+7 (4942) 63-49-42',
    email: 'extreme.kostroma@yandex.ru',
    image: 'https://radarextreme.ru/upload/iblock/f16/aekiii2865p63aqc41ehhbdex1hv7k88.jpg',
    brands: [
      'Yamaha', 'Stels', 'Salut', 'Volzhanka', 'Marlin', 'Hidea', 'Gladiator',
    ],
  },
  {
    name: 'Radar Extreme Кинешма',
    city: 'Кинешма',
    address: 'Россия, Ивановская область, Кинешма, Савинская улица, 17Б',
    phone: '+7(4932)24-55-30',
    email: 'yamaha@radar-avto.ru',
    image: 'https://radarextreme.ru/upload/iblock/eb9/eb93665187b839b47f1c30bd92c2b39b.jpg',
    brands: [
      'Kayo', 'Marlin', 'Volzhanka', 'Phoenix', 'Hidea',
      'Gladiator', 'Stels', 'Bajaj', 'Salut',
    ],
  },
  {
    name: 'Radar Extreme Владимир',
    city: 'Владимир',
    address: 'Россия, Владимир, улица Опольевская, 1А',
    phone: '+7 (4922) 49-48-49',
    email: 'yamaha@radar-avto.ru',
    image: 'https://radarextreme.ru/upload/iblock/115/xt0ir0x52wp6re3nngskw42lq9rjac3w.jpg',
    brands: [
      'Русская механика', 'AODES', 'GLADIATOR', 'Marlin', 'Hidea',
      'ZONTES', 'Волжанка', 'Триера', 'Салют', 'M1NSK', 'Progasi',
      'Voge', 'Regulmoto', 'Ataki', 'CYCLONE', 'Lonchin', 'KOVE', 'GAOKIN',
    ],
  },
]
</script>

<style scoped>
.salons {
  padding: 48px 0;
}
.salons__title {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 40px;
  color: var(--color-text);
}
.salons__grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 28px;
}
.salon-card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: 14px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.salon-card:hover {
  border-color: var(--color-accent);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}
.salon-card__image {
  width: 100%;
  height: 220px;
  overflow: hidden;
  flex-shrink: 0;
}
.salon-card__img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
}
.salon-card__placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
}
.salon-card__icon {
  width: 48px;
  height: 48px;
  color: var(--color-accent);
  opacity: 0.7;
}
.salon-card__placeholder-text {
  font-size: 18px;
  font-weight: 700;
  color: var(--color-text);
  opacity: 0.5;
  letter-spacing: 0.04em;
}
.salon-card__body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.salon-card__name {
  font-size: 20px;
  font-weight: 700;
  color: var(--color-text);
  margin-bottom: 2px;
}
.salon-card__city {
  font-size: 14px;
  color: var(--color-muted);
  margin-bottom: 16px;
}
.salon-card__info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.salon-card__row {
  display: flex;
  gap: 8px;
  font-size: 14px;
  line-height: 1.5;
}
.salon-card__label {
  color: var(--color-muted);
  white-space: nowrap;
  flex-shrink: 0;
  min-width: 70px;
}
.salon-card__value {
  color: var(--color-text);
}
.salon-card__value--link {
  color: var(--color-accent);
  text-decoration: none;
  transition: opacity 0.2s;
}
.salon-card__value--link:hover {
  opacity: 0.75;
}
.salon-card__row--brands {
  flex-direction: column;
  gap: 8px;
}
.salon-card__brands {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.salon-card__brand {
  display: inline-block;
  background: rgba(255, 107, 0, 0.12);
  color: var(--color-accent);
  font-size: 12px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 999px;
  letter-spacing: 0.01em;
}

@media (max-width: 600px) {
  .salons {
    padding: 32px 16px;
  }
  .salons__grid {
    grid-template-columns: 1fr;
  }
}
</style>