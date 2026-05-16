# アプリケーションカタログ レビュー

bear-app の **Application 層 UseCase 群** を「アプリケーション機能のカタログ」として評価する文書です。コード品質（PHPMD メトリクス）ではなく、**機能の抽象化・契約・網羅性・再利用性** という観点でカタログとしての性能を判定します。

---

## 1. 目的

Application 層に並ぶ UseCase は、本アプリケーションの **業務能力（capability）の目録** として機能します。

- 外部からは「アプリができること」のインデックスとして読まれる
- 上位層（Resource / CLI Command）はこのカタログから能力を選んで呼び出す
- 新規開発者・統合者にとっては機能の発見入口になる

したがってカタログには **粒度の均一性・命名の一貫性・契約の自己説明性・副作用の予測性** が求められます。本文書はこの観点で現状を点検します。

## 2. 読み方

- 各 UseCase は「業務能力 1 件」を表すべきという前提を取る
- スコアは ★1〜5（高いほど良い）
- 「契約」= `InputData` / `OutputData` の自己説明性
- 「副作用可視性」= 名前から実行される副作用（メール送信等）が予測できるか
- 該当ファイルへのリンクは相対パスで記載

## 3. 評価軸

| 軸 | 問い |
|---|---|
| **命名** | ユースケース名が業務能力を端的に表しているか |
| **抽象度** | 粒度は適切か（薄すぎ／詰め込み過ぎでないか） |
| **契約** | InputData/OutputData がカタログとして自己説明的か |
| **副作用の可視性** | 名前から副作用（メール送信等）が予測できるか |
| **再利用性** | 他コンテキスト（ユーザー側等）に転用可能か |
| **ドメイン整合** | ドメイン語彙と一致しているか |

## 4. カタログ一覧と個別評価

### 4.1 評価表

| # | UseCase | 命名 | 抽象度 | 契約 | 副作用可視性 | カタログ価値 |
|---|---------|:---:|:---:|:---:|:---:|:---:|
| 1 | [CreateAdminUseCase](../ddd/core/src/Application/Admin/CreateAdminUseCase.php) | ★★★★ | ★★ | ★★ | ★ | **★★★** |
| 2 | [CreateAdminEmailUseCase](../ddd/core/src/Application/Admin/CreateAdminEmailUseCase.php) | ★★★ | ★★ | ★★ | ★ | **★★** |
| 3 | [DeleteAdminUseCase](../ddd/core/src/Application/Admin/DeleteAdminUseCase.php) | ★★★★ | ★★★★ | ★★★ | ★★ | **★★★★** |
| 4 | [DeleteAdminEmailUseCase](../ddd/core/src/Application/Admin/DeleteAdminEmailUseCase.php) | ★★★★★ | ★★★★★ | ★★★★ | ★★★★★ | **★★★★★** |
| 5 | [ForgotAdminPasswordUseCase](../ddd/core/src/Application/Admin/ForgotAdminPasswordUseCase.php) | ★★★★★ | ★★★★ | ★★★★ | ★★★ | **★★★★** |
| 6 | [GetAdminUseCase](../ddd/core/src/Application/Admin/GetAdminUseCase.php) | ★★★★ | ★★ | ★★★★ | ★★★★★ | **★★** ※薄い |
| 7 | [GetForgotAdminPasswordUseCase](../ddd/core/src/Application/Admin/GetForgotAdminPasswordUseCase.php) | ★ | ★★ | ★ | ★★ | **★** ※Get なのに何も返さない |
| 8 | [GetJoinedAdminUseCase](../ddd/core/src/Application/Admin/GetJoinedAdminUseCase.php) | ★ | ★★ | ★ | ★★ | **★** ※#7 と完全重複概念 |
| 9 | [JoinAdminUserCase](../ddd/core/src/Application/Admin/JoinAdminUserCase.php) | ★ | ★★★ | ★★★★ | ★★ | **★★** ※"Join" の語彙が曖昧＋タイポ |
| 10 | [ResetAdminPasswordUseCase](../ddd/core/src/Application/Admin/ResetAdminPasswordUseCase.php) | ★★★★★ | ★★★★ | ★★★ | ★★★ | **★★★★** |
| 11 | [UpdateAdminPasswordUseCase](../ddd/core/src/Application/Admin/UpdateAdminPasswordUseCase.php) | ★★★★★ | ★★★★ | ★★★ | ★★★ | **★★★★** |
| 12 | [VerifyAdminEmailUseCase](../ddd/core/src/Application/Admin/VerifyAdminEmailUseCase.php) | ★★★★★ | ★★★★ | ★★★ | ★★★ | **★★★★** |
| 13 | [ImportBadPasswordUseCase](../ddd/core/src/Application/Command/ImportBadPasswordUseCase.php) | ★★★★ | ★★★ | ★★★★ | ★★★ | **★★★** |
| 14 | [SendEmailFromEmailQueueUseCase](../ddd/core/src/Application/Command/SendEmailFromEmailQueueUseCase.php) | ★★★★★ | ★★★★★ | ★★★★ | ★★★★★ | **★★★★★** |
| 15 | [GetVerificationCodeUseCase](../ddd/core/src/Application/GetVerificationCodeUseCase.php) | ★★★★ | ★★ | ★★★ | ★★★★ | **★★** ※Query ラッパー |
| 16 | [VerifyVerificationCodeUseCase](../ddd/core/src/Application/VerifyVerificationCodeUseCase.php) | ★★★★★ | ★★★★ | ★★★★ | ★★★★ | **★★★★** |

### 4.2 グレード分布

| カタログ価値 | 件数 | 該当 |
|--------:|----:|------|
| ★★★★★ | 2 | #4, #14 |
| ★★★★ | 6 | #3, #5, #10, #11, #12, #16 |
| ★★★ | 2 | #1, #13 |
| ★★ | 4 | #2, #6, #9, #15 |
| ★ | 2 | #7, #8 |

## 5. カタログ全体としての性能

### 5.1 強み

- **InputData / OutputData パターンの徹底**: 全 UseCase で契約が型として明示されており、カタログ化しやすい
- **動詞先頭命名**: `Create / Update / Delete / Verify / Reset / Send / Import` と業務動詞が揃っており、カタログ閲覧者が引きやすい
- **Admin ライフサイクルの網羅**: 招待 → 参加 → 認証 → パスワード管理 → メール管理 → 削除 までの能力が揃っている
- **コマンドライン能力との対称性**: `SendEmailFromEmailQueue` / `ImportBadPassword` が Web フローと同じ抽象レベルで並ぶ

### 5.2 弱み

| 弱み | 該当 | カタログへの影響 |
|---|---|---|
| **粒度の不均衡** | #6, #15 | Query 直呼びと同等の薄い UseCase が混入し、「能力」より「データアクセサ」に見える |
| **動詞の意味乖離** | #7, #8 | `Get*` が void を返す（実態は「URL 署名期限の検証」）。閲覧者が誤解する |
| **概念重複** | #7 / #8 | 完全同形の UseCase が別名で並ぶ |
| **副作用の不可視** | #1, #2 | 名前に「メール送信」が無いのに 2 通送る。副作用が予測不能 |
| **責務肥大** | #2 | 「メール追加 + 検証メール送信 + 既存全宛通知」を抱える。能力単位が曖昧 |
| **戻り値の不揃い** | #1 vs #5, #9 | `CreateAdmin` は ID を返さず、`Forgot` / `Join` は URL を返す。上位層から扱いにくい |
| **対称性の欠落** | User 側 | User 系 UseCase が Application 層にほぼ無く、Admin に偏重 |
| **命名タイポ** | #9 | `JoinAdminUserCase`（正: `UseCase`）。カタログのインデックス信頼性が低下 |

### 5.3 総合判定

| 観点 | 評価 |
|---|---|
| 個別能力としての品質 | ★★★★ |
| カタログ全体としての完成度 | ★★★ |

業務動詞ベースの命名と契約の型化は優れている一方、粒度のばらつき・命名の意味乖離・対称性の欠落により、カタログ全体としてはまだ磨き込み余地があります。

## 6. 抽象化の改善ロードマップ

優先度の高い順に示します。

### 6.1 命名タイポの修正（即時）

- `JoinAdminUserCase` → `JoinAdminUseCase`
- ファイル名・クラス名・参照箇所の一括リネーム

### 6.2 「Get なのに void」を解消（短期）

- `GetForgotAdminPasswordUseCase` / `GetJoinedAdminUseCase` を `VerifyUrlSignatureUseCase`（または `EnsureSignatureNotExpiredUseCase`）に統合
- 2 つを 1 つに集約することで概念重複も解消

### 6.3 薄い UseCase の整理（短期）

- `GetAdminUseCase` / `GetVerificationCodeUseCase` は Resource から `AdminQueryInterface` / `VerificationCodeQueryInterface` を直接呼ぶ形に変更し、Application 層には残さない
- 業務能力ではなく単なるデータ取得は Query レイヤーへ寄せる

### 6.4 副作用の名前への昇格（中期）

- `CreateAdminUseCase` から「ウェルカムメール送信」を別 UseCase（`SendAdminWelcomeMailUseCase`）に分離、または Domain Event 経由で発火
- `CreateAdminEmailUseCase` から「既存全宛通知」を `NotifyAdminEmailAddedUseCase` として独立
- 名前から副作用が予測できる状態に整える

### 6.5 OutputData の標準化（中期）

- 作成系 UseCase は必ず生成エンティティの ID（または最小限の識別子）を返す
- `CreateAdminOutputData` に `adminId` を追加

### 6.6 ドメイン語彙の見直し（中期）

- `Join` の語彙が曖昧（招待コード送信に近い）。`InviteAdminUseCase` / `RequestAdminInvitationUseCase` 等への改名を検討
- 業界標準語彙（IAM/Identity 文脈）との整合をとる

### 6.7 User 側カタログの整備（長期）

- `CreateUserUseCase` / `DeleteUserUseCase` / `Verify*UserCase` 等、Admin と対称な User 側 UseCase を揃える
- カタログとしての対称性を確保し、新規開発者の予測可能性を高める

## 7. 改善後のカタログ像（イメージ）

```text
Application/
├── Admin/
│   ├── InviteAdminUseCase            (旧 JoinAdminUserCase: 招待コード送信)
│   ├── CreateAdminUseCase            (招待受諾 → 管理者作成、ID を返す)
│   ├── DeleteAdminUseCase
│   ├── UpdateAdminPasswordUseCase
│   ├── ResetAdminPasswordUseCase
│   ├── ForgotAdminPasswordUseCase
│   ├── AddAdminEmailUseCase          (旧 CreateAdminEmailUseCase: 追加のみ)
│   ├── VerifyAdminEmailUseCase
│   └── DeleteAdminEmailUseCase
├── User/                              (Admin と対称な能力群)
│   └── ...
├── Notification/                      (副作用を昇格させた通知系)
│   ├── SendAdminWelcomeMailUseCase
│   ├── NotifyAdminEmailAddedUseCase
│   └── NotifyAdminPasswordChangedUseCase
├── Command/
│   ├── ImportBadPasswordUseCase
│   └── SendEmailFromEmailQueueUseCase
└── Shared/
    ├── VerifyUrlSignatureUseCase     (旧 GetForgot/GetJoined を統合)
    └── VerifyVerificationCodeUseCase
```

この形に整えると **ファイル一覧 = アプリケーションが提供する能力カタログ** として、外部に提示できるレベルになります。

## 8. 次のアクション

1. 本文書をベースに Issue を起票し、Roadmap 6.1〜6.3 をクイックウィンとして先行着手
2. 6.4〜6.6 はリファクタリング PR としてまとめて提案
3. 6.7 は新機能開発として別途計画

---

**作成日**: 2026-05-16
**対象ブランチ**: `claude/interesting-bhaskara-54ecd9`
**評価対象**: `source/app/ddd/core/src/Application/` 配下の全 16 UseCase
