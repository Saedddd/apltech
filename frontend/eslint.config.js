// eslint.config.js
import js from '@eslint/js'
import pluginVue from 'eslint-plugin-vue'
import pluginTs from '@typescript-eslint/eslint-plugin'
import parserTs from '@typescript-eslint/parser'
import pluginPrettier from 'eslint-plugin-prettier'
import configPrettier from 'eslint-config-prettier'
import globals from 'globals'

export default [
  // Базовые рекомендации ESLint
  js.configs.recommended,

  // Рекомендации для Vue
  ...pluginVue.configs['flat/recommended'],

  // Настройки для TypeScript
  {
    files: ['**/*.ts', '**/*.tsx', '**/*.vue'],
    languageOptions: {
      parser: parserTs,
      parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
        extraFileExtensions: ['.vue'],
      },
    },
    plugins: {
      '@typescript-eslint': pluginTs,
    },
    rules: {
      ...pluginTs.configs.recommended.rules,
      '@typescript-eslint/no-explicit-any': 'warn',
      '@typescript-eslint/no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
    },
  },

  // Глобальные настройки для всех файлов
  {
    files: ['**/*.{js,ts,vue}'],
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
        ...globals.es2021,
      },
    },
    plugins: {
      prettier: pluginPrettier,
    },
    rules: {
      // Prettier
      'prettier/prettier': 'error',

      // Общие правила
      'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-debugger': process.env.NODE_ENV === 'production' ? 'warn' : 'off',
      'no-var': 'error',
      'prefer-const': 'error',
    },
  },

  // Специальные правила для Vue файлов
  {
    files: ['**/*.vue'],
    languageOptions: {
      parserOptions: {
        parser: parserTs,
      },
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/no-v-html': 'warn',
      'vue/require-default-prop': 'off',
      'vue/attribute-hyphenation': 'off',
    },
  },

  // Игнорируемые файлы/папки
  {
    ignores: [
      'dist/**',
      'node_modules/**',
      '*.d.ts',
      'vite.config.ts',
      'tailwind.config.js',
      'postcss.config.js',
    ],
  },

  // Отключение конфликтующих правил Prettier
  configPrettier,
]
