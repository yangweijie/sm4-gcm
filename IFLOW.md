# 项目概述

此项目包含两个主要部分：

1.  **kona-crypto**: 这是一个 Java 库，实现了中国的商用密码算法（ShangMi），包括 SM2、SM3 和 SM4。它基于腾讯的 Kona Crypto 项目，并提供了纯 Java 实现以及基于 JNI 和 OpenSSL 的实现。
2.  **PHP SM4-GCM**: 这是一个 PHP 项目，旨在移植 Java 的 SM4-GCM 功能。

## Kona Crypto (Java)

### 简介

Tencent Kona Crypto 包含三个 Java 安全提供者：
*   `KonaCrypto` (纯 Java 实现)
*   `KonaCrypto-Native` (基于 JNI 和 OpenSSL)
*   `KonaCrypto-NativeOneShot` (基于 JNI 和 OpenSSL，但内存管理方式不同)

它实现了以下 ShangMi 算法：
*   **SM2**: 基于椭圆曲线密码学 (ECC) 的公钥算法。
*   **SM3**: 密码哈希算法。
*   **SM4**: 分组加密算法。

### 构建和运行

该项目使用 Gradle 作为构建工具。

*   **构建项目**: 在 `kona-crypto` 目录下运行 `./gradlew build`。
*   **运行测试**: 在 `kona-crypto` 目录下运行 `./gradlew test`。
*   **生成 JNI 头文件**: 在 `kona-crypto` 目录下运行 `./gradlew genJNIHeaders`。

### 关键文件和目录

*   `src/main/java/`: Java 源代码。
*   `src/main/jni/`: JNI 相关的 C 代码和头文件。
*   `src/test/java/`: 测试代码。
*   `build.gradle.kts`: Gradle 构建脚本。
*   `README.md`: 项目的详细说明文档。

## PHP SM4-GCM

### 简介

这是一个 PHP 项目，其目标是移植 Java Kona Crypto 中的 SM4-GCM 功能。从 `composer.json` 文件可以看出，它依赖于 `yangweijie/gm-helper` 包。

### 目录结构

*   `src/`: PHP 源代码目录（当前为空）。
*   `vendor/`: Composer 依赖包目录。
*   `composer.json`: Composer 配置文件。
*   `kona-crypto/`: 包含 Java 实现的 Kona Crypto 库。

### 使用方法

目前 `src/` 目录为空，因此尚无法直接使用。预计未来会在此目录下添加实现 SM4-GCM 加密解密功能的 PHP 代码。