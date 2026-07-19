// production/assets/i18n.js — 静的サイト用 ja/zh/zh-TW/en/ko 辞書と自動言語切替（navigator.languages 優先）。
(function () {
  "use strict";

  /** @typedef {'ja'|'zh'|'zh-TW'|'en'|'ko'} LocaleId */

  /** @type {LocaleId} */
  let activeLocale = "ja";

  /** 初回は HTML で Poppins のみ非同期読み込み。CJK はロケール確定後に追加。 */
  /** @type {Partial<Record<LocaleId, string>>} */
  const LOCALE_FONT_URLS = {
    ja: "https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&display=swap",
    zh: "https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;600;700&display=swap",
    "zh-TW": "https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;600;700&display=swap",
    ko: "https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700&display=swap",
  };

  /** @type {Set<string>} */
  const loadedLocaleFontUrls = new Set();

  /** 言語メニューは各言語のネイティブ表記で固定（サイト表示言語に依存しない） */
  const LANG_OPTION_LABELS = {
    optJa: "日本語 (JA/JP)",
    optZh: "中文（简体）(ZH)",
    optZhTw: "中文（繁體・台灣）(ZH-TW)",
    optEn: "English (EN)",
    optKo: "한국어 (KO)",
  };

  /**
   * ロケールに応じた CJK フォントを必要なときだけ読み込む。
   * @param {LocaleId} locale
   * @returns {Promise<void>}
   */
  function ensureLocaleFonts(locale) {
    const url = LOCALE_FONT_URLS[locale];
    if (!url || loadedLocaleFontUrls.has(url)) return Promise.resolve();
    loadedLocaleFontUrls.add(url);
    return new Promise((resolve) => {
      const link = document.createElement("link");
      link.rel = "stylesheet";
      link.href = url;
      link.onload = () => resolve();
      link.onerror = () => resolve();
      document.head.appendChild(link);
    });
  }

  /**
   * 手動選択は `localStorage.site-locale`（'ja'|'zh'|'zh-TW'|'en'|'ko'）があれば最優先。ヘッダーの言語メニューから設定されます。
   * @type {Record<LocaleId, Record<string, unknown>>}
   */
  const TRANSLATIONS = {
    ja: {
      meta: { title: "松山南高校 松山空港ジオラマプロジェクト" },
      loader: { text: "読み込み中…" },
      brand: { schoolShort: "松山南高校" },
      aria: {
        menuOpen: "メニューを開く",
        newsList: "お知らせ一覧",
        newsPager: "お知らせのページ",
        newsPrev: "前のページ",
        newsNext: "次のページ",
        footerNav: "フッターナビ",
      },
      nav: {
        about: "活動紹介",
        achievements: "実績",
        awards: "受賞歴",
        members: "メンバー",
        virtual: "バーチャル空間",
        dioramaAr: "ARジオラマ",
      },
      lang: {
        menuButtonAria: "言語を選ぶ",
        ...LANG_OPTION_LABELS,
      },
      hero: {
        eyebrow: "松山南高校 自然科学部 プロジェクト",
        titleHtml: "触れて、歩いて、<br>松山空港を体験する。",
        lead:
          "3Dプリンタで再現したジオラマと、誰でもアクセスできるバーチャル空間。視覚障がいのある生徒や、世界中の旅行者に、松山の魅力を新しい方法で届けます。",
        btnVirtual: "バーチャル空間へ",
        btnAbout: "プロジェクトについて",
      },
      img: {
        heroAlt: "松山空港ジオラマ",
        blenderAlt: "Blender 3D制作ワークフロー",
        touchAlt: "触察体験",
        wristbandAlt: "活動用腕章",
        placardAlt: "空ジオプロジェクトの活動プラカード",
        virtualAlt: "バーチャル空間体験",
      },
      news: {
        title: "お知らせ",
        item1: "お知らせのサンプルです。活動紹介セクションへリンクしています。",
        item2: "リンクなしの例です。公開準備中のメッセージなどに使えます。",
        item3: "バーチャル空間の案内（アンカーリンクの例）",
        empty: "お知らせはまだありません。",
        pageGroupAria: "お知らせ {current} / {total} ページ",
        pagerPageAria: "ページ {n}",
      },
      about: {
        eyebrow: "About the project",
        title: "活動紹介",
        subtitle:
          "松山市からの依頼を受け、特別支援学校の修学旅行支援として、松山の観光名所をジオラマで「触察」してもらう活動に取り組んでいます。",
      },
      process: {
        sectionTitle: "制作プロセス",
        s1Title: "Blenderで3Dモデリング",
        s1Body:
          "松山空港の建築を詳細にモデリング。3Dプリント対応の簡略版と、バーチャル空間用の詳細版の両方を制作します。",
        s2Title: "3Dプリンターで出力",
        s2Body:
          "モデルを3Dプリンターで印刷。視覚障がい者が触察しやすいように、適切なサイズと質感を実現します。",
        s3Title: "バーチャル空間で公開",
        s3Body:
          "詳細にモデリングしたバージョンをバーチャル空間に展開。外国人観光客も松山空港の構造を理解できます。",
      },
      purpose: {
        title: "プロジェクトの目的",
        item1Title: "視覚障がい者への配慮",
        item1Body:
          "特別支援学校の生徒が触察を通じて松山空港の構造を理解し、修学旅行の学習をサポートします。",
        item2Title: "外国人観光客への情報提供",
        item2Body: "バーチャル空間を通じて、言語の壁を越えて松山空港の魅力を世界に発信します。",
      },
      achievements: {
        eyebrow: "Track record",
        title: "これまでの活動",
        subtitle: "松山市の観光支援事業の一環として、これまでに3つのジオラマプロジェクトを制作してきました。",
      },
      awards: {
        eyebrow: "Awards",
        title: "受賞歴",
        subtitle: "大会やコンテストでの主な受賞・選考結果をご紹介します。",
        linkMore: "詳細を見る",
        detailMore: "↑ 詳しく",
        carouselHint: "ドラッグまたは矢印で切り替え",
        thumbPlaceholder: "サムネ画像",
        attachmentsLabel: "資料",
        attachmentsEmpty: "添付ファイルはありません",
        empty: "受賞歴はまだありません。",
        status: {
          won: "受賞",
          finalist: "ファイナリスト",
          first_pass: "1次通過",
          ongoing: "進行中",
          nominated: "ノミネート",
        },
      },
      timeline: { past: "これまで", ongoing: "進行中" },
      achv1: {
        title: "松山城ジオラマ",
        desc: "プロジェクトの第一弾。松山市の象徴である松山城を3Dプリンターで制作し、特別支援学校の生徒の触察体験に活用しました。",
        year: "令和5年度",
      },
      achv2: {
        title: "道後温泉ジオラマ",
        desc: "第二弾として、より複雑な建築構造を持つ道後温泉本館に挑戦。形状の再現精度を大きく向上させました。",
        year: "令和6年度",
      },
      achv3: {
        title: "松山空港ジオラマ",
        tagline: "with バーチャル空間",
        desc: "第三弾。ジオラマに加えて、誰でもアクセスできるバーチャル空間も同時展開し、外国人観光客にも松山の魅力を発信します。",
        year: "令和7年度",
      },
      touch: {
        title: "触察を通じた学習",
        subtitle:
          "このプロジェクトは、視覚障がい者が手で触れることで、建築物の構造や空間配置を直感的に理解できるように設計されています。",
        b1Title: "直感的な理解",
        b1Body: "視覚情報に頼らず、触覚を通じて空間を認識できます。",
        b2Title: "修学旅行の事前学習",
        b2Body: "実際に訪問する前に、ジオラマで松山の観光名所を学べます。",
        b3Title: "インクルーシブな体験",
        b3Body: "すべての人が等しく松山の魅力を体験できる環境を実現します。",
      },
      members: {
        eyebrow: "Team",
        title: "メンバー紹介",
        subtitle:
          "松山南高校自然科学部の生徒たちが、それぞれの担当領域でプロジェクトを推進しています。",
        m1role: "リーダー、モデリング、印刷担当",
        m2role: "モデリング、メタバースサーバー・ファイル共有システム構築担当",
        m3role: "モデリング、メタバース化、印刷担当",
        m4role: "空港のスキャン、撮影担当",
        m5role: "空港のスキャン、撮影担当",
      },
      partner: {
        eyebrow: "Partners",
        title: "協力者・パートナー",
        subtitle: "このプロジェクトは、松山市および関連機関のご協力により実現しています。",
        roleMain: "事業主体・支援",
        roleFacility: "施設協力・情報提供",
        roleCoop: "協力",
        p1Name: "松山市産業経済部",
        p2Name: "松山空港ビル株式会社",
        p3Name: "ANAエアサービス松山",
        p4Name: "日本航空 松山空港所",
        p5Name: "ANAFESTA松山店",
        p6Name: "今治タオル 松山エアポートストア",
        p7Name: "伊予鉄商事",
        p8Name: "株式会社 和光ビルサービス",
        p1desc:
          "松山市からの依頼を受け、観光支援事業の一環としてプロジェクトを推進しています。",
        p2desc:
          "松山空港の詳細な建築情報や施設情報を提供。バーチャル空間での正確な再現をサポートしています。",
      },
      activity: {
        title: "活動について",
        subtitle: "このプロジェクトの透明性と安全性についてご説明します。",
        b1Title: "公式な活動腕章",
        b1Body:
          "松山南高校の生徒は、このプロジェクトの公式な活動を示す腕章を着用して活動しています。松山空港での撮影活動は、松山市および松山空港ビル株式会社の協力のもと、許可を得て実施されています。",
        b2Title: "プライバシーと安全性の配慮",
        b2Body:
          "撮影活動では、個人が特定されるような撮影は行いません。また、保安区域の撮影も厳密に禁止されており、安全保障上の配慮を最優先としています。",
        b3Title: "教育的な意義",
        b3Body:
          "このプロジェクトは、特別支援学校の生徒や外国人観光客に松山の魅力を伝えるための、純粋な教育的活動です。ご理解とご協力をお願いいたします。",
        capWristband: "松山南高校 空ジオラマプロジェクトの活動腕章",
        capPlacard: "松山南高校 空ジオラマプロジェクトの活動プラカード",
      },
      virtual: {
        title: "バーチャル空間で体験",
        subtitle:
          "詳細にモデリングした松山空港をバーチャル空間に展開。世界中どこからでも、3D空間で松山空港を探索できます。",
        f1Title: "3D空間での自由な探索",
        f1Body: "松山空港の内部構造を360度自由に観察できます。",
        f2Title: "多言語対応",
        f2Body: "外国人観光客も松山空港の情報を理解しやすくなります。",
        f3Title: "インタラクティブな体験",
        f3Body: "チャット、スタンプ、ビデオ通話など、コミュニケーション機能も搭載。",
        howtoTitle: "操作方法",
        controlsEyebrow: "Controls",
        kbdMouse: "マウス",
        howtoRow1: "視点変更",
        howtoRow2Html: "移動（<kbd>Shift</kbd>+<kbd>W</kbd>でダッシュ）",
        howtoRow3: "ジャンプ",
        howtoRow4: "カーソル表示",
        howtoRow5dtHtml: "設定 ⚙",
        howtoRow5dd: "一人称 / 三人称の切替",
        btnAccess: "バーチャル空間へアクセス",
        btnSimulator: "航空機シミュレーターへアクセス",
        galleryRegionRow1Aria: "バーチャル空間の紹介動画と画面 1〜2（上段）",
        galleryRegionRow2Aria: "バーチャル空間の画面 3〜5と紹介動画（下段）",
        galleryRailAria: "表示する画面を選ぶ",
        galleryPlayVideo: "動画を再生",
        galleryAltMeta: "バーチャル空間の紹介動画",
        galleryAlt1: "バーチャル空間の画面 1",
        galleryAlt2: "バーチャル空間の画面 2",
        galleryAlt3: "バーチャル空間の画面 3",
        galleryAlt4: "バーチャル空間の画面 4",
        galleryAlt5: "バーチャル空間の画面 5",
        galleryAlt6: "バーチャル空間の紹介動画",
      },
      arDiorama: {
        title: "ジオラマを体験",
        subtitle:
          "スマートフォンから、松山空港ジオラマをARで机の上に表示できます。実物のジオラマの雰囲気を、手のひらサイズでお試しください。",
        f1Title: "机の上に配置",
        f1Body: "カメラで水平な面を認識し、タップしてジオラマを置けます。",
        f2Title: "スマートフォン専用",
        f2Body: "iPhone・Android のスマートフォンでご利用いただけます（カメラ許可が必要です）。",
        mobileNote: "明るい場所で、机や床などの水平な面の上でお試しください。",
        btnLaunch: "ARでジオラマを体験",
        launchAr: "ARでジオラマを体験",
        pageTitle: "ARジオラマ体験",
        backHome: "トップへ戻る",
        howtoTitle: "操作方法",
        howtoStep1: "「ARでジオラマを体験」をタップし、カメラの使用を許可します。",
        howtoStep2: "スマホをゆっくり動かし、机や床などの水平な面を認識させます。",
        howtoStep3: "画面をタップして、松山空港ジオラマを配置します。",
        howtoStep4: "AR中は2本指のピンチで拡大・縮小できます。",
        howtoStep5: "2本指でスライドしてオブジェクトを移動できます（Androidのブラウザ内ARではモデル上を1本指でドラッグ）。",
        howtoStep6: "右端のスライダーでモデルの大きさを調整できます（最小0.1%）。",
        scaleLabel: "サイズ",
        scaleAria: "モデルの大きさ",
        scaleSliderAria: "モデルの大きさを調整",
        unsupported:
          "この端末ではARをご利用いただけません。下の画面で3Dモデルを回転・拡大してご覧いただけます。",
        loadingHint: "モデルを読み込んでいます…",
        previewAlt: "松山空港ジオラマ",
        viewerAlt: "松山空港ジオラマ",
      },
      contact: {
        title: "お問い合わせはこちら",
        lead: "ご質問・ご協力のお申し出などは、下記メールアドレスまでご連絡ください。",
      },
      footer: {
        col1Title: "松山南高校",
        col1Body:
          "愛媛県松山市にある高等学校。自然科学部が中心となって、松山の観光支援プロジェクトに取り組んでいます。",
        col2Title: "プロジェクト",
        col3Title: "リンク",
        schoolHp: "学校ホームページ",
        virtualSpace: "バーチャル空間",
        officialInstagram: "公式 Instagram",
        copyrightBefore: "© ",
        copyrightAfter: " 松山南高校 自然科学部. All rights reserved.",
      },
    },
    zh: {
      meta: { title: "松山南高中 松山机场立体模型项目" },
      loader: { text: "加载中…" },
      brand: { schoolShort: "松山南高中" },
      aria: {
        menuOpen: "打开菜单",
        newsList: "公告列表",
        newsPager: "公告分页",
        newsPrev: "上一页",
        newsNext: "下一页",
        footerNav: "页脚导航",
      },
      nav: {
        about: "活动介绍",
        achievements: "成果",
        awards: "获奖记录",
        members: "成员",
        virtual: "虚拟空间",
        dioramaAr: "AR立体模型",
      },
      lang: {
        menuButtonAria: "选择页面语言",
        ...LANG_OPTION_LABELS,
      },
      hero: {
        eyebrow: "松山南高中 自然科学部 项目",
        titleHtml: "触摸、行走、<br>体验松山机场。",
        lead:
          "用3D打印机还原的立体模型，以及人人可访问的虚拟空间。以全新方式，向视障学生和全球旅行者呈现松山的魅力。",
        btnVirtual: "进入虚拟空间",
        btnAbout: "关于项目",
      },
      img: {
        heroAlt: "松山机场立体模型",
        blenderAlt: "Blender 三维制作流程",
        touchAlt: "触觉探索体验",
        wristbandAlt: "活动臂章",
        placardAlt: "空ジオ项目活动手举牌",
        virtualAlt: "虚拟空间体验",
      },
      news: {
        title: "公告",
        item1: "示例公告。链接至活动介绍版块。",
        item2: "无链接示例。可用于“公开准备中”等信息。",
        item3: "虚拟空间说明（锚点链接示例）",
        empty: "暂无公告。",
        pageGroupAria: "公告 第 {current} / {total} 页",
        pagerPageAria: "第 {n} 页",
      },
      about: {
        eyebrow: "关于项目",
        title: "活动介绍",
        subtitle:
          "受松山市委托，作为特别支援学校修学旅行支援的一环，通过立体模型开展“触觉参观”松山名胜的活动。",
      },
      process: {
        sectionTitle: "制作流程",
        s1Title: "使用 Blender 进行三维建模",
        s1Body:
          "精细建模松山机场建筑。同时制作适合3D打印的简化版与用于虚拟空间的精细版。",
        s2Title: "3D 打印输出",
        s2Body: "用3D打印机输出模型，兼顾便于触觉探索的尺寸与质感。",
        s3Title: "在虚拟空间发布",
        s3Body: "将精细建模版本部署到虚拟空间，帮助外国游客理解松山机场结构。",
      },
      purpose: {
        title: "项目目的",
        item1Title: "面向视障人士的关怀",
        item1Body: "特别支援学校的学生通过触觉理解松山机场结构，支持修学旅行学习。",
        item2Title: "向外国游客提供信息",
        item2Body: "通过虚拟空间跨越语言障碍，向世界传播松山机场的魅力。",
      },
      achievements: {
        eyebrow: "成果记录",
        title: "以往活动",
        subtitle: "作为松山市旅游支援事业的一部分，至今已制作三个立体模型项目。",
      },
      awards: {
        eyebrow: "Awards",
        title: "获奖记录",
        subtitle: "介绍在各类大赛与评选中的主要获奖及选拔结果。",
        linkMore: "查看详情",
        detailMore: "↑ 详情",
        carouselHint: "拖动或使用箭头切换",
        thumbPlaceholder: "缩略图",
        attachmentsLabel: "资料",
        attachmentsEmpty: "暂无附件",
        empty: "暂无获奖记录。",
        status: {
          won: "获奖",
          finalist: "决赛入围",
          first_pass: "初审通过",
          ongoing: "进行中",
          nominated: "提名",
        },
      },
      timeline: { past: "以往", ongoing: "进行中" },
      achv1: {
        title: "松山城立体模型",
        desc: "项目第一弹。用3D打印机制作象征松山市的松山城，用于特别支援学校学生的触觉体验。",
        year: "令和5年度",
      },
      achv2: {
        title: "道后温泉立体模型",
        desc: "第二弹挑战结构更复杂的道后温泉本馆，显著提高形状还原精度。",
        year: "令和6年度",
      },
      achv3: {
        title: "松山机场立体模型",
        tagline: "with 虚拟空间",
        desc: "第三弹。除立体模型外，同步推出人人可访问的虚拟空间，向外国游客传播松山魅力。",
        year: "令和7年度",
      },
      touch: {
        title: "通过触觉学习",
        subtitle: "本项目旨在让视障人士用手触摸，直观理解建筑结构与空间布局。",
        b1Title: "直观理解",
        b1Body: "不依赖视觉，通过触觉认识空间。",
        b2Title: "修学旅行预习",
        b2Body: "在实地到访前，可通过立体模型学习松山名胜。",
        b3Title: "包容性体验",
        b3Body: "营造人人都能平等体验松山魅力的环境。",
      },
      members: {
        eyebrow: "团队",
        title: "成员介绍",
        subtitle: "松山南高中自然科学部学生各自负责不同领域，推进本项目。",
        m1role: "负责人、建模、打印",
        m2role: "建模、元宇宙服务器与文件共享系统",
        m3role: "建模、元宇宙化、打印",
        m4role: "机场扫描与拍摄",
        m5role: "机场扫描与拍摄",
      },
      partner: {
        eyebrow: "Partners",
        title: "合作与支持方",
        subtitle: "本项目在松山市及相关机构的协助下得以实现。",
        roleMain: "主办·支持",
        roleFacility: "设施合作·信息提供",
        roleCoop: "合作",
        p1Name: "松山市产业经济部",
        p2Name: "松山机场大楼株式会社",
        p3Name: "ANA航空服务松山",
        p4Name: "日本航空松山机场办事处",
        p5Name: "ANAFESTA松山店",
        p6Name: "今治毛巾松山机场店",
        p7Name: "伊予铁商事",
        p8Name: "和光楼宇服务株式会社",
        p1desc: "受松山市委托，作为旅游支援事业的一环推进项目。",
        p2desc: "提供松山机场详细建筑与设施信息，支持在虚拟空间中准确还原。",
      },
      activity: {
        title: "关于活动",
        subtitle: "说明本项目的透明度与安全保障。",
        b1Title: "官方活动臂章",
        b1Body:
          "松山南高中学生佩戴表明本项目官方活动的臂章。在机场的拍摄活动在松山市与松山机场大楼株式会社的协助下获得许可后进行。",
        b2Title: "隐私与安全",
        b2Body:
          "拍摄避免可识别个人身份的画面；严禁拍摄保安区域，将安全置于首位。",
        b3Title: "教育意义",
        b3Body:
          "本项目纯粹面向特别支援学校学生与外国游客介绍松山魅力。敬请理解与配合。",
        capWristband: "松山南高中 空ジオラマ项目活动臂章",
        capPlacard: "松山南高中 空ジオラマ项目活动花絮",
      },
      virtual: {
        title: "在虚拟空间体验",
        subtitle: "将精细建模的松山机场部署到虚拟空间。全球用户都可在三维空间中探索松山机场。",
        f1Title: "在三维空间中自由探索",
        f1Body: "可360度自由观察松山机场内部结构。",
        f2Title: "多语言支持",
        f2Body: "外国游客更容易理解松山机场信息。",
        f3Title: "互动体验",
        f3Body: "配备聊天、表情贴、视频通话等交流功能。",
        howtoTitle: "操作说明",
        controlsEyebrow: "Controls",
        kbdMouse: "鼠标",
        howtoRow1: "视角操作",
        howtoRow2Html: "移动（<kbd>Shift</kbd>+<kbd>W</kbd> 冲刺）",
        howtoRow3: "跳跃",
        howtoRow4: "显示光标",
        howtoRow5dtHtml: "设置 ⚙",
        howtoRow5dd: "第一人称 / 第三人称切换",
        btnAccess: "进入虚拟空间",
        btnSimulator: "进入飞机模拟器",
        galleryRegionRow1Aria: "虚拟空间介绍视频与画面 1〜2（上）",
        galleryRegionRow2Aria: "虚拟空间画面 3〜5 与介绍视频（下）",
        galleryRailAria: "选择要显示的画面",
        galleryPlayVideo: "播放视频",
        galleryAltMeta: "虚拟空间介绍视频",
        galleryAlt1: "虚拟空间画面 1",
        galleryAlt2: "虚拟空间画面 2",
        galleryAlt3: "虚拟空间画面 3",
        galleryAlt4: "虚拟空间画面 4",
        galleryAlt5: "虚拟空间画面 5",
        galleryAlt6: "虚拟空间介绍视频",
      },
      arDiorama: {
        title: "体验立体模型",
        subtitle:
          "用手机将松山机场立体模型以AR方式显示在桌面上。在掌中感受实体立体模型的氛围。",
        f1Title: "放置在桌面上",
        f1Body: "通过摄像头识别水平面，点击即可放置立体模型。",
        f2Title: "仅限智能手机",
        f2Body: "可在 iPhone 与 Android 手机上使用（需允许使用摄像头）。",
        mobileNote: "请在明亮环境下，于桌面或地板等水平面上体验。",
        btnLaunch: "用AR体验立体模型",
        launchAr: "用AR体验立体模型",
        pageTitle: "AR立体模型体验",
        backHome: "返回首页",
        howtoTitle: "操作说明",
        howtoStep1: "点击“用AR体验立体模型”，并允许使用摄像头。",
        howtoStep2: "缓慢移动手机，让系统识别桌面或地板等水平面。",
        howtoStep3: "点击屏幕，放置松山机场立体模型。",
        howtoStep4: "AR中可用双指捏合放大或缩小。",
        howtoStep5: "双指滑动可移动对象（Android浏览器内AR请用单指在模型上拖动）。",
        howtoStep6: "使用右侧滑块调整模型大小（最小0.1%）。",
        scaleLabel: "大小",
        scaleAria: "模型大小",
        scaleSliderAria: "调整模型大小",
        unsupported: "此设备无法使用AR。您可在下方画面中旋转、缩放查看3D模型。",
        loadingHint: "正在加载模型…",
        previewAlt: "松山机场立体模型",
        viewerAlt: "松山机场立体模型",
      },
      contact: {
        title: "联系方式",
        lead: "如有疑问或希望合作，请通过以下邮箱联系。",
      },
      footer: {
        col1Title: "松山南高中",
        col1Body: "位于爱媛县松山市的高中。自然科学部主导开展松山旅游支援项目。",
        col2Title: "项目",
        col3Title: "链接",
        schoolHp: "学校官网",
        virtualSpace: "虚拟空间",
        officialInstagram: "官方 Instagram",
        copyrightBefore: "© ",
        copyrightAfter: " 松山南高中 自然科学部. 保留所有权利。",
      },
    },
    "zh-TW": {
      meta: { title: "松山南高中 松山機場立體模型專案" },
      loader: { text: "載入中…" },
      brand: { schoolShort: "松山南高中" },
      aria: {
        menuOpen: "開啟選單",
        newsList: "公告列表",
        newsPager: "公告分頁",
        newsPrev: "上一頁",
        newsNext: "下一頁",
        footerNav: "頁尾導覽",
      },
      nav: {
        about: "活動介紹",
        achievements: "成果",
        awards: "獲獎紀錄",
        members: "成員",
        virtual: "虛擬空間",
        dioramaAr: "AR立體模型",
      },
      lang: {
        menuButtonAria: "選擇頁面語言",
        ...LANG_OPTION_LABELS,
      },
      hero: {
        eyebrow: "松山南高中 自然科學部 專案",
        titleHtml: "觸摸、行走、<br>體驗松山機場。",
        lead:
          "用3D列印機還原的立體模型，以及人人可存取的虛擬空間。以全新方式，向視障學生與全球旅行者呈現松山的魅力。",
        btnVirtual: "進入虛擬空間",
        btnAbout: "關於專案",
      },
      img: {
        heroAlt: "松山機場立體模型",
        blenderAlt: "Blender 三維製作流程",
        touchAlt: "觸覺探索體驗",
        wristbandAlt: "活動臂章",
        placardAlt: "空ジオ專案活動手舉牌",
        virtualAlt: "虛擬空間體驗",
      },
      news: {
        title: "公告",
        item1: "範例公告。連結至活動介紹區塊。",
        item2: "無連結範例。可用於「公開準備中」等資訊。",
        item3: "虛擬空間說明（錨點連結範例）",
        empty: "暫無公告。",
        pageGroupAria: "公告 第 {current} / {total} 頁",
        pagerPageAria: "第 {n} 頁",
      },
      about: {
        eyebrow: "關於專案",
        title: "活動介紹",
        subtitle:
          "受松山市委託，作為特別支援學校修學旅行支援的一環，透過立體模型開展「觸覺參觀」松山名勝的活動。",
      },
      process: {
        sectionTitle: "製作流程",
        s1Title: "使用 Blender 進行三維建模",
        s1Body:
          "精細建模松山機場建築。同時製作適合3D列印的簡化版與用於虛擬空間的精細版。",
        s2Title: "3D 列印輸出",
        s2Body: "用3D列印機輸出模型，兼顧便於觸覺探索的尺寸與質感。",
        s3Title: "在虛擬空間發布",
        s3Body: "將精細建模版本部署到虛擬空間，幫助外國遊客理解松山機場結構。",
      },
      purpose: {
        title: "專案目的",
        item1Title: "面向視障人士的關懷",
        item1Body: "特別支援學校的學生透過觸覺理解松山機場結構，支援修學旅行學習。",
        item2Title: "向外國遊客提供資訊",
        item2Body: "透過虛擬空間跨越語言隔閡，向世界傳播松山機場的魅力。",
      },
      achievements: {
        eyebrow: "成果紀錄",
        title: "以往活動",
        subtitle: "作為松山市旅遊支援事業的一部分，至今已製作三個立體模型專案。",
      },
      awards: {
        eyebrow: "Awards",
        title: "獲獎紀錄",
        subtitle: "介紹在各類大賽與評選中的主要獲獎及選拔結果。",
        linkMore: "查看詳情",
        detailMore: "↑ 詳情",
        carouselHint: "拖曳或使用箭頭切換",
        thumbPlaceholder: "縮圖",
        attachmentsLabel: "資料",
        attachmentsEmpty: "尚無附件",
        empty: "尚無獲獎紀錄。",
        status: {
          won: "獲獎",
          finalist: "決賽入圍",
          first_pass: "初審通過",
          ongoing: "進行中",
          nominated: "提名",
        },
      },
      timeline: { past: "以往", ongoing: "進行中" },
      achv1: {
        title: "松山城立體模型",
        desc: "專案第一彈。用3D列印機製作象徵松山市的松山城，用於特別支援學校學生的觸覺體驗。",
        year: "令和5年度",
      },
      achv2: {
        title: "道後溫泉立體模型",
        desc: "第二彈挑戰結構更複雜的道後溫泉本館，顯著提高形狀還原精度。",
        year: "令和6年度",
      },
      achv3: {
        title: "松山機場立體模型",
        tagline: "with 虛擬空間",
        desc: "第三彈。除立體模型外，同步推出人人可存取的虛擬空間，向外國遊客傳播松山魅力。",
        year: "令和7年度",
      },
      touch: {
        title: "透過觸覺學習",
        subtitle: "本專案旨在讓視障人士用手觸摸，直觀理解建築結構與空間配置。",
        b1Title: "直觀理解",
        b1Body: "不依賴視覺，透過觸覺認識空間。",
        b2Title: "修學旅行預習",
        b2Body: "在實地到訪前，可透過立體模型學習松山名勝。",
        b3Title: "包容性體驗",
        b3Body: "營造人人都能平等體驗松山魅力的環境。",
      },
      members: {
        eyebrow: "團隊",
        title: "成員介紹",
        subtitle: "松山南高中自然科學部學生各自負責不同領域，推進本專案。",
        m1role: "負責人、建模、列印",
        m2role: "建模、元宇宙伺服器與檔案共享系統",
        m3role: "建模、元宇宙化、列印",
        m4role: "機場掃描與拍攝",
        m5role: "機場掃描與拍攝",
      },
      partner: {
        eyebrow: "Partners",
        title: "合作與支援方",
        subtitle: "本專案在松山市及相關機構的協助下得以實現。",
        roleMain: "主辦・支援",
        roleFacility: "設施合作・資訊提供",
        roleCoop: "合作",
        p1Name: "松山市產業經濟部",
        p2Name: "松山機場大樓株式會社",
        p3Name: "ANA航空服務松山",
        p4Name: "日本航空松山機場辦事處",
        p5Name: "ANAFESTA松山店",
        p6Name: "今治毛巾松山機場店",
        p7Name: "伊予鐵商事",
        p8Name: "株式會社 和光大樓服務",
        p1desc: "受松山市委託，作為旅遊支援事業的一環推進專案。",
        p2desc: "提供松山機場詳細建築與設施資訊，支援在虛擬空間中準確還原。",
      },
      activity: {
        title: "關於活動",
        subtitle: "說明本專案的透明度與安全保障。",
        b1Title: "官方活動臂章",
        b1Body:
          "松山南高中學生佩戴表明本專案官方活動的臂章。在機場的拍攝活動在松山市與松山機場大樓株式會社的協助下獲得許可後進行。",
        b2Title: "隱私與安全",
        b2Body:
          "拍攝避免可識別個人身分的畫面；嚴禁拍攝保安區域，將安全置於首位。",
        b3Title: "教育意義",
        b3Body:
          "本專案純粹面向特別支援學校學生與外國遊客介紹松山魅力。敬請理解與配合。",
        capWristband: "松山南高中 空ジオラマ專案活動臂章",
        capPlacard: "松山南高中 空ジオラマ專案活動花絮",
      },
      virtual: {
        title: "在虛擬空間體驗",
        subtitle: "將精細建模的松山機場部署到虛擬空間。全球使用者都可在三維空間中探索松山機場。",
        f1Title: "在三維空間中自由探索",
        f1Body: "可360度自由觀察松山機場內部結構。",
        f2Title: "多語言支援",
        f2Body: "外國遊客更容易理解松山機場資訊。",
        f3Title: "互動體驗",
        f3Body: "配備聊天、貼圖、視訊通話等交流功能。",
        howtoTitle: "操作說明",
        controlsEyebrow: "Controls",
        kbdMouse: "滑鼠",
        howtoRow1: "視角操作",
        howtoRow2Html: "移動（<kbd>Shift</kbd>+<kbd>W</kbd> 衝刺）",
        howtoRow3: "跳躍",
        howtoRow4: "顯示游標",
        howtoRow5dtHtml: "設定 ⚙",
        howtoRow5dd: "第一人稱 / 第三人稱切換",
        btnAccess: "進入虛擬空間",
        btnSimulator: "進入飛機模擬器",
        galleryRegionRow1Aria: "虛擬空間介紹影片與畫面 1〜2（上段）",
        galleryRegionRow2Aria: "虛擬空間畫面 3〜5 與介紹影片（下段）",
        galleryRailAria: "選擇要顯示的畫面",
        galleryPlayVideo: "播放影片",
        galleryAltMeta: "虛擬空間介紹影片",
        galleryAlt1: "虛擬空間畫面 1",
        galleryAlt2: "虛擬空間畫面 2",
        galleryAlt3: "虛擬空間畫面 3",
        galleryAlt4: "虛擬空間畫面 4",
        galleryAlt5: "虛擬空間畫面 5",
        galleryAlt6: "虛擬空間介紹影片",
      },
      arDiorama: {
        title: "體驗立體模型",
        subtitle:
          "用手機將松山機場立體模型以AR方式顯示在桌面上。在掌中感受實體立體模型的氛圍。",
        f1Title: "放置在桌面上",
        f1Body: "透過相機辨識水平面，點擊即可放置立體模型。",
        f2Title: "僅限智慧型手機",
        f2Body: "可在 iPhone 與 Android 手機上使用（需允許使用相機）。",
        mobileNote: "請在明亮環境下，於桌面或地板等水平面上體驗。",
        btnLaunch: "用AR體驗立體模型",
        launchAr: "用AR體驗立體模型",
        pageTitle: "AR立體模型體驗",
        backHome: "返回首頁",
        howtoTitle: "操作說明",
        howtoStep1: "點擊「用AR體驗立體模型」，並允許使用相機。",
        howtoStep2: "緩慢移動手機，讓系統辨識桌面或地板等水平面。",
        howtoStep3: "點擊螢幕，放置松山機場立體模型。",
        howtoStep4: "AR中可用雙指捏合放大或縮小。",
        howtoStep5: "雙指滑動可移動物件（Android瀏覽器內AR請用單指在模型上拖曳）。",
        howtoStep6: "使用右側滑塊調整模型大小（最小0.1%）。",
        scaleLabel: "大小",
        scaleAria: "模型大小",
        scaleSliderAria: "調整模型大小",
        unsupported: "此裝置無法使用AR。您可在下方畫面中旋轉、縮放檢視3D模型。",
        loadingHint: "正在載入模型…",
        previewAlt: "松山機場立體模型",
        viewerAlt: "松山機場立體模型",
      },
      contact: {
        title: "聯絡方式",
        lead: "如有疑問或希望合作，請透過以下電子郵件聯繫。",
      },
      footer: {
        col1Title: "松山南高中",
        col1Body: "位於愛媛縣松山市的高中。自然科學部主導開展松山旅遊支援專案。",
        col2Title: "專案",
        col3Title: "連結",
        schoolHp: "學校官網",
        virtualSpace: "虛擬空間",
        officialInstagram: "官方 Instagram",
        copyrightBefore: "© ",
        copyrightAfter: " 松山南高中 自然科學部. 保留所有權利。",
      },
    },
    en: {
      meta: { title: "Matsuyama Minami High School — Matsuyama Airport Diorama Project" },
      loader: { text: "Loading…" },
      brand: { schoolShort: "Matsuyama Minami High School" },
      aria: {
        menuOpen: "Open menu",
        newsList: "News list",
        newsPager: "News pagination",
        newsPrev: "Previous page",
        newsNext: "Next page",
        footerNav: "Footer navigation",
      },
      nav: {
        about: "About",
        achievements: "Achievements",
        awards: "Awards",
        members: "Team",
        virtual: "Virtual space",
        dioramaAr: "AR diorama",
      },
      lang: {
        menuButtonAria: "Choose page language",
        ...LANG_OPTION_LABELS,
      },
      hero: {
        eyebrow: "Matsuyama Minami High School — Science Club Project",
        titleHtml: "Touch, walk, and<br>experience Matsuyama Airport.",
        lead:
          "A 3D-printed diorama and a virtual space anyone can access. We bring Matsuyama closer—in a new way—to students with visual impairments and travelers worldwide.",
        btnVirtual: "Enter virtual space",
        btnAbout: "About the project",
      },
      img: {
        heroAlt: "Matsuyama Airport diorama",
        blenderAlt: "Blender 3D production workflow",
        touchAlt: "Tactile exploration",
        wristbandAlt: "Official activity wristband",
        placardAlt: "Project activity placard",
        virtualAlt: "Virtual space experience",
      },
      news: {
        title: "News",
        item1: "Sample news item linking to the About section.",
        item2: "Example without a link—use for “coming soon” messages.",
        item3: "Virtual space notice (anchor link example)",
        empty: "No news yet.",
        pageGroupAria: "News page {current} of {total}",
        pagerPageAria: "Page {n}",
      },
      about: {
        eyebrow: "About the project",
        title: "About the project",
        subtitle:
          "Commissioned by Matsuyama City, we support school trips for special-needs schools by letting students explore Matsuyama’s sights through tactile dioramas.",
      },
      process: {
        sectionTitle: "Production process",
        s1Title: "3D modeling in Blender",
        s1Body:
          "We model Matsuyama Airport in detail—both a print-friendly simplified version and a detailed version for the virtual space.",
        s2Title: "3D printing",
        s2Body:
          "We print the models at sizes and textures suited to tactile exploration for people with visual impairments.",
        s3Title: "Publishing in virtual space",
        s3Body:
          "We deploy the detailed model online so international visitors can understand the airport’s layout.",
      },
      purpose: {
        title: "Project goals",
        item1Title: "Support for people with visual impairments",
        item1Body:
          "Students at special-needs schools learn the airport’s structure through touch, supporting their school-trip learning.",
        item2Title: "Information for international visitors",
        item2Body:
          "Through virtual space, we share Matsuyama Airport’s appeal across language barriers.",
      },
      achievements: {
        eyebrow: "Track record",
        title: "What we’ve done so far",
        subtitle:
          "As part of Matsuyama City’s tourism support programs, we have produced three diorama projects.",
      },
      awards: {
        eyebrow: "Awards",
        title: "Awards & recognition",
        subtitle: "Highlights from competitions and selection processes we have taken part in.",
        linkMore: "Learn more",
        detailMore: "↑ Details",
        carouselHint: "Drag or use arrows to browse",
        thumbPlaceholder: "Thumbnail",
        attachmentsLabel: "Materials",
        attachmentsEmpty: "No attachments",
        empty: "No awards to display yet.",
        status: {
          won: "Awarded",
          finalist: "Finalist",
          first_pass: "Passed round 1",
          ongoing: "In progress",
          nominated: "Nominated",
        },
      },
      timeline: { past: "Past", ongoing: "In progress" },
      achv1: {
        title: "Matsuyama Castle diorama",
        desc: "Our first project: a 3D-printed Matsuyama Castle—Matsuyama’s symbol—for tactile learning at a special-needs school.",
        year: "FY2023 (Reiwa 5)",
      },
      achv2: {
        title: "Dōgo Onsen diorama",
        desc: "Our second project: a more complex structure—the Dōgo Onsen main building—with greatly improved shape accuracy.",
        year: "FY2024 (Reiwa 6)",
      },
      achv3: {
        title: "Matsuyama Airport diorama",
        tagline: "with virtual space",
        desc: "Our third project: alongside the diorama, we launched an accessible virtual space to reach international visitors.",
        year: "FY2025 (Reiwa 7)",
      },
      touch: {
        title: "Learning through touch",
        subtitle:
          "The project is designed so people with visual impairments can grasp building structure and spatial layout by hand.",
        b1Title: "Intuitive understanding",
        b1Body: "Recognize space through touch, not only vision.",
        b2Title: "Preparation before school trips",
        b2Body: "Learn about Matsuyama’s sights on the diorama before visiting in person.",
        b3Title: "Inclusive experience",
        b3Body: "We aim for an environment where everyone can enjoy Matsuyama equally.",
      },
      members: {
        eyebrow: "Team",
        title: "Team",
        subtitle:
          "Students of the science club at Matsuyama Minami High School drive the project in their respective roles.",
        m1role: "Lead, modeling, printing",
        m2role: "Modeling, metaverse server & file sharing",
        m3role: "Modeling, metaverse integration, printing",
        m4role: "Airport scanning & photography",
        m5role: "Airport scanning & photography",
      },
      partner: {
        eyebrow: "Partners",
        title: "Partners",
        subtitle: "This project is realized with support from Matsuyama City and related organizations.",
        roleMain: "Project owner · support",
        roleFacility: "Facility cooperation · information",
        roleCoop: "Cooperation",
        p1Name: "Matsuyama City Industrial Policy Division",
        p2Name: "Matsuyama Airport Terminal Building Co., Ltd.",
        p3Name: "ANA Air Services Matsuyama",
        p4Name: "JAL Matsuyama Airport Office",
        p5Name: "ANAFESTA Matsuyama",
        p6Name: "Imabari Towel Matsuyama Airport Store",
        p7Name: "Iyo Tetsu Commerce Co., Ltd.",
        p8Name: "Wako Building Service Co., Ltd.",
        p1desc:
          "We advance the project under commission from Matsuyama City as part of its tourism support programs.",
        p2desc:
          "Detailed architectural and facility information supports accurate reproduction in virtual space.",
      },
      activity: {
        title: "About our activities",
        subtitle: "Transparency and safety of this project.",
        b1Title: "Official activity wristbands",
        b1Body:
          "Students wear official wristbands during project activities. Filming at Matsuyama Airport is conducted with permission from the city and Matsuyama Airport Terminal Building Co., Ltd.",
        b2Title: "Privacy and safety",
        b2Body:
          "We avoid imagery that could identify individuals. Filming in security zones is strictly prohibited; safety comes first.",
        b3Title: "Educational purpose",
        b3Body:
          "This is a purely educational effort for special-needs students and international visitors. Thank you for your understanding and cooperation.",
        capWristband: "Matsuyama Minami HS — Sora-diorama project wristband",
        capPlacard: "Matsuyama Minami HS — Sora-diorama project snapshot",
      },
      virtual: {
        title: "Experience it in virtual space",
        subtitle:
          "Explore a detailed Matsuyama Airport model online—from anywhere in the world—in full 3D.",
        f1Title: "Free exploration in 3D",
        f1Body: "Observe the airport interior from any angle.",
        f2Title: "Multilingual support",
        f2Body: "Makes airport information easier for international visitors to understand.",
        f3Title: "Interactive features",
        f3Body: "Includes chat, stamps, and video calls.",
        howtoTitle: "Controls",
        controlsEyebrow: "Controls",
        kbdMouse: "Mouse",
        howtoRow1: "Camera / look",
        howtoRow2Html: "Move (<kbd>Shift</kbd> + <kbd>W</kbd> to sprint)",
        howtoRow3: "Jump",
        howtoRow4: "Release cursor",
        howtoRow5dtHtml: "Settings ⚙",
        howtoRow5dd: "First / third person",
        btnAccess: "Open virtual space",
        btnSimulator: "Open aircraft simulator",
        galleryRegionRow1Aria: "Virtual space intro video and screenshots 1–2 (top row)",
        galleryRegionRow2Aria: "Virtual space screenshots 3–5 and intro video (bottom row)",
        galleryRailAria: "Choose a view to display",
        galleryPlayVideo: "Play video",
        galleryAltMeta: "Virtual space introduction video",
        galleryAlt1: "Virtual space screenshot 1",
        galleryAlt2: "Virtual space screenshot 2",
        galleryAlt3: "Virtual space screenshot 3",
        galleryAlt4: "Virtual space screenshot 4",
        galleryAlt5: "Virtual space screenshot 5",
        galleryAlt6: "Virtual space introduction video",
      },
      arDiorama: {
        title: "Experience the diorama",
        subtitle:
          "View the Matsuyama Airport diorama on a table in AR from your smartphone—a palm-sized preview of the physical model.",
        f1Title: "Place on a table",
        f1Body: "Detect a flat surface with the camera, then tap to place the diorama.",
        f2Title: "Smartphones only",
        f2Body: "Works on iPhone and Android phones (camera permission required).",
        mobileNote: "Use in a bright area on a horizontal surface such as a desk or floor.",
        btnLaunch: "View diorama in AR",
        launchAr: "View diorama in AR",
        pageTitle: "AR diorama experience",
        backHome: "Back to home",
        howtoTitle: "How to use",
        howtoStep1: "Tap “View diorama in AR” and allow camera access.",
        howtoStep2: "Move your phone slowly to detect a horizontal surface.",
        howtoStep3: "Tap the screen to place the Matsuyama Airport diorama.",
        howtoStep4: "In AR, pinch with two fingers to resize the model.",
        howtoStep5: "Slide with two fingers to move the object (on Android in-browser AR, drag on the model with one finger).",
        howtoStep6: "Use the slider on the right edge to resize the model (minimum 0.1%).",
        scaleLabel: "Size",
        scaleAria: "Model size",
        scaleSliderAria: "Adjust model size",
        unsupported:
          "AR is not available on this device. You can still rotate and zoom the 3D model below.",
        loadingHint: "Loading model…",
        previewAlt: "Matsuyama Airport diorama",
        viewerAlt: "Matsuyama Airport diorama",
      },
      contact: {
        title: "Contact",
        lead: "For questions or collaboration, please email us at the address below.",
      },
      footer: {
        col1Title: "Matsuyama Minami High School",
        col1Body:
          "A high school in Matsuyama, Ehime Prefecture. The science club leads tourism support projects for the city.",
        col2Title: "Project",
        col3Title: "Links",
        schoolHp: "School website",
        virtualSpace: "Virtual space",
        officialInstagram: "Official Instagram",
        copyrightBefore: "© ",
        copyrightAfter: " Matsuyama Minami High School, Science Club. All rights reserved.",
      },
    },
    ko: {
      meta: { title: "마쓰야마 미나미 고등학교 마쓰야마 공항 디오라마 프로젝트" },
      loader: { text: "불러오는 중…" },
      brand: { schoolShort: "마쓰야마 미나미 고등학교" },
      aria: {
        menuOpen: "메뉴 열기",
        newsList: "공지 목록",
        newsPager: "공지 페이지",
        newsPrev: "이전 페이지",
        newsNext: "다음 페이지",
        footerNav: "하단 내비게이션",
      },
      nav: {
        about: "활동 소개",
        achievements: "성과",
        awards: "수상 이력",
        members: "멤버",
        virtual: "가상 공간",
        dioramaAr: "AR 디오라마",
      },
      lang: {
        menuButtonAria: "페이지 언어 선택",
        ...LANG_OPTION_LABELS,
      },
      hero: {
        eyebrow: "마쓰야마 미나미 고등학교 자연과학부 프로젝트",
        titleHtml: "만지고, 걸으며,<br>마쓰야마 공항을 체험한다.",
        lead:
          "3D 프린터로 재현한 디오라마와 누구나 이용할 수 있는 가상 공간. 시각장애가 있는 학생과 전 세계 여행객에게 마쓰야마의 매력을 새로운 방식으로 전합니다.",
        btnVirtual: "가상 공간으로",
        btnAbout: "프로젝트 소개",
      },
      img: {
        heroAlt: "마쓰야마 공항 디오라마",
        blenderAlt: "Blender 3D 제작 워크플로",
        touchAlt: "촉각 탐색 체험",
        wristbandAlt: "활동용 완장",
        placardAlt: "空ジオ 프로젝트 활동 플래카드",
        virtualAlt: "가상 공간 체험",
      },
      news: {
        title: "공지",
        item1: "샘플 공지입니다. 활동 소개 섹션으로 연결됩니다.",
        item2: "링크가 없는 예입니다. ‘공개 준비 중’ 등의 메시지에 쓸 수 있습니다.",
        item3: "가상 공간 안내(앵커 링크 예)",
        empty: "아직 공지가 없습니다.",
        pageGroupAria: "공지 {current} / {total} 페이지",
        pagerPageAria: "{n}페이지",
      },
      about: {
        eyebrow: "프로젝트 소개",
        title: "활동 소개",
        subtitle:
          "마쓰야마 시의 의뢰를 받아, 특수지원학교 수학여행 지원의 일환으로 마쓰야마의 관광 명소를 디오라마로 ‘촉각 관람’하게 하는 활동에 임하고 있습니다.",
      },
      process: {
        sectionTitle: "제작 프로세스",
        s1Title: "Blender로 3D 모델링",
        s1Body:
          "마쓰야마 공항 건축을 세밀하게 모델링합니다. 3D 프린트용 간략판과 가상 공간용 상세판을 모두 제작합니다.",
        s2Title: "3D 프린터로 출력",
        s2Body:
          "모델을 3D 프린터로 출력합니다. 시각장애인이 촉각으로 탐색하기 쉽도록 적절한 크기와 질감을 구현합니다.",
        s3Title: "가상 공간에 공개",
        s3Body:
          "상세하게 모델링한 버전을 가상 공간에 배치합니다. 외국인 관광객도 마쓰야마 공항의 구조를 이해할 수 있습니다.",
      },
      purpose: {
        title: "프로젝트 목적",
        item1Title: "시각장애인에 대한 배려",
        item1Body:
          "특수지원학교 학생이 촉각을 통해 마쓰야마 공항의 구조를 이해하고, 수학여행 학습을 지원합니다.",
        item2Title: "외국인 관광객에게 정보 제공",
        item2Body: "가상 공간을 통해 언어의 장벽을 넘어 마쓰야마 공항의 매력을 세계에 알립니다.",
      },
      achievements: {
        eyebrow: "성과 기록",
        title: "지금까지의 활동",
        subtitle: "마쓰야마 시 관광 지원 사업의 일환으로 지금까지 세 가지 디오라마 프로젝트를 제작해 왔습니다.",
      },
      awards: {
        eyebrow: "Awards",
        title: "수상 이력",
        subtitle: "대회 및 콘테스트에서의 주요 수상·선발 결과를 소개합니다.",
        linkMore: "자세히 보기",
        detailMore: "↑ 자세히",
        carouselHint: "드래그하거나 화살표로 전환",
        thumbPlaceholder: "썸네일",
        attachmentsLabel: "자료",
        attachmentsEmpty: "첨부 파일 없음",
        empty: "수상 이력이 아직 없습니다.",
        status: {
          won: "수상",
          finalist: "파이널리스트",
          first_pass: "1차 통과",
          ongoing: "진행 중",
          nominated: "노미네이트",
        },
      },
      timeline: { past: "지금까지", ongoing: "진행 중" },
      achv1: {
        title: "마쓰야마성 디오라마",
        desc: "프로젝트 첫 작품. 마쓰야마의 상징인 마쓰야마성을 3D 프린터로 제작하여 특수지원학교 학생의 촉각 체험에 활용했습니다.",
        year: "레이와 5년도",
      },
      achv2: {
        title: "도고 온천 디오라마",
        desc: "두 번째 작품으로, 더 복잡한 건축 구조를 가진 도고 온천 본관에 도전하여 형상 재현 정밀도를 크게 향상시켰습니다.",
        year: "레이와 6년도",
      },
      achv3: {
        title: "마쓰야마 공항 디오라마",
        tagline: "with 가상 공간",
        desc: "세 번째 작품. 디오라마에 더해 누구나 이용할 수 있는 가상 공간도 동시에 전개하여 외국인 관광객에게도 마쓰야마의 매력을 알립니다.",
        year: "레이와 7년도",
      },
      touch: {
        title: "촉각을 통한 학습",
        subtitle:
          "이 프로젝트는 시각장애인이 손으로 만져 건축물의 구조와 공간 배치를 직관적으로 이해할 수 있도록 설계되어 있습니다.",
        b1Title: "직관적 이해",
        b1Body: "시각 정보에만 의존하지 않고 촉각을 통해 공간을 인식할 수 있습니다.",
        b2Title: "수학여행 사전 학습",
        b2Body: "실제 방문 전에 디오라마로 마쓰야마의 관광 명소를 배울 수 있습니다.",
        b3Title: "포용적 체험",
        b3Body: "모든 사람이 동등하게 마쓰야마의 매력을 체험할 수 있는 환경을 실현합니다.",
      },
      members: {
        eyebrow: "팀",
        title: "멤버 소개",
        subtitle:
          "마쓰야마 미나미 고등학교 자연과학부 학생들이 각자 담당 분야에서 프로젝트를 추진하고 있습니다.",
        m1role: "리더, 모델링, 출력 담당",
        m2role: "모델링, 메타버스 서버·파일 공유 시스템 구축 담당",
        m3role: "모델링, 메타버스화, 출력 담당",
        m4role: "공항 스캔·촬영 담당",
        m5role: "공항 스캔·촬영 담당",
      },
      partner: {
        eyebrow: "Partners",
        title: "협력자·파트너",
        subtitle: "이 프로젝트는 마쓰야마 시 및 관련 기관의 협력으로 실현되고 있습니다.",
        roleMain: "사업 주체·지원",
        roleFacility: "시설 협력·정보 제공",
        roleCoop: "협력",
        p1Name: "마쓰야마 시 산업경제부",
        p2Name: "마쓰야마 공항빌딩 주식회사",
        p3Name: "ANA 에어서비스 마쓰야마",
        p4Name: "일본항공 마쓰야마 공항 사무소",
        p5Name: "ANAFESTA 마쓰야마점",
        p6Name: "이마바리 타월 마쓰야마 에어포트 스토어",
        p7Name: "이요테츠 상사",
        p8Name: "주식회사 와코 빌 서비스",
        p1desc:
          "마쓰야마 시의 의뢰를 받아 관광 지원 사업의 일환으로 프로젝트를 추진하고 있습니다.",
        p2desc:
          "마쓰야마 공항의 상세한 건축·시설 정보를 제공합니다. 가상 공간에서의 정확한 재현을 지원합니다.",
      },
      activity: {
        title: "활동에 대해",
        subtitle: "이 프로젝트의 투명성과 안전에 대해 설명합니다.",
        b1Title: "공식 활동 완장",
        b1Body:
          "마쓰야마 미나미 고등학교 학생은 이 프로젝트의 공식 활동을 나타내는 완장을 착용하고 활동합니다. 마쓰야마 공항에서의 촬영 활동은 마쓰야마 시 및 마쓰야마 공항빌딩 주식회사의 협력 아래 허가를 받아 실시됩니다.",
        b2Title: "프라이버시와 안전",
        b2Body:
          "촬영 활동에서는 개인이 특정될 수 있는 촬영은 하지 않습니다. 또한 보안 구역 촬영은 엄격히 금지되어 있으며 안전을 최우선으로 합니다.",
        b3Title: "교육적 의의",
        b3Body:
          "이 프로젝트는 특수지원학교 학생과 외국인 관광객에게 마쓰야마의 매력을 전하기 위한 순수한 교육 활동입니다. 이해와 협조를 부탁드립니다.",
        capWristband: "마쓰야마 미나미 고등학교 空ジオラマ 프로젝트 활동 완장",
        capPlacard: "마쓰야마 미나미 고등학교 空ジオラマ 프로젝트 활동 스냅",
      },
      virtual: {
        title: "가상 공간에서 체험",
        subtitle:
          "세밀하게 모델링한 마쓰야마 공항을 가상 공간에 전개합니다. 전 세계 어디서나 3D 공간에서 마쓰야마 공항을 탐색할 수 있습니다.",
        f1Title: "3D 공간에서의 자유로운 탐색",
        f1Body: "마쓰야마 공항 내부 구조를 360도 자유롭게 관찰할 수 있습니다.",
        f2Title: "다국어 지원",
        f2Body: "외국인 관광객도 마쓰야마 공항 정보를 이해하기 쉬워집니다.",
        f3Title: "인터랙티브 체험",
        f3Body: "채팅, 스탬프, 영상 통화 등 커뮤니케이션 기능도 탑재되어 있습니다.",
        howtoTitle: "조작 방법",
        controlsEyebrow: "Controls",
        kbdMouse: "마우스",
        howtoRow1: "시점 변경",
        howtoRow2Html: "이동(<kbd>Shift</kbd>+<kbd>W</kbd>로 대시)",
        howtoRow3: "점프",
        howtoRow4: "커서 표시",
        howtoRow5dtHtml: "설정 ⚙",
        howtoRow5dd: "1인칭 / 3인칭 전환",
        btnAccess: "가상 공간에 액세스",
        btnSimulator: "항공기 시뮬레이터에 액세스",
        galleryRegionRow1Aria: "가상 공간 소개 동영상과 화면 1〜2(상단)",
        galleryRegionRow2Aria: "가상 공간 화면 3〜5와 소개 동영상(하단)",
        galleryRailAria: "표시할 화면 선택",
        galleryPlayVideo: "동영상 재생",
        galleryAltMeta: "가상 공간 소개 동영상",
        galleryAlt1: "가상 공간 화면 1",
        galleryAlt2: "가상 공간 화면 2",
        galleryAlt3: "가상 공간 화면 3",
        galleryAlt4: "가상 공간 화면 4",
        galleryAlt5: "가상 공간 화면 5",
        galleryAlt6: "가상 공간 소개 동영상",
      },
      arDiorama: {
        title: "디오라마 체험",
        subtitle:
          "스마트폰으로 마쓰야마 공항 디오라마를 AR로 책상 위에 표시할 수 있습니다. 실물 디오라마의 분위기를 손안에서 체험해 보세요.",
        f1Title: "책상 위에 배치",
        f1Body: "카메라로 수평면을 인식한 뒤 탭하여 디오라마를 놓을 수 있습니다.",
        f2Title: "스마트폰 전용",
        f2Body: "iPhone·Android 스마트폰에서 이용할 수 있습니다(카메라 허용 필요).",
        mobileNote: "밝은 곳에서 책상이나 바닥 등 수평면 위에서 체험해 주세요.",
        btnLaunch: "AR로 디오라마 체험",
        launchAr: "AR로 디오라마 체험",
        pageTitle: "AR 디오라마 체험",
        backHome: "홈으로 돌아가기",
        howtoTitle: "조작 방법",
        howtoStep1: "「AR로 디오라마 체험」을 탭하고 카메라 사용을 허용합니다.",
        howtoStep2: "스마트폰을 천천히 움직여 책상이나 바닥 등 수평면을 인식시킵니다.",
        howtoStep3: "화면을 탭하여 마쓰야마 공항 디오라마를 배치합니다.",
        howtoStep4: "AR 중 두 손가락으로 핀치하여 크기를 조절할 수 있습니다.",
        howtoStep5: "두 손가락으로 슬라이드하여 객체를 이동할 수 있습니다(Android 브라우저 AR에서는 모델 위를 한 손가락으로 드래그).",
        howtoStep6: "오른쪽 슬라이더로 모델 크기를 조절할 수 있습니다(최소 0.1%).",
        scaleLabel: "크기",
        scaleAria: "모델 크기",
        scaleSliderAria: "모델 크기 조절",
        unsupported:
          "이 기기에서는 AR을 사용할 수 없습니다. 아래 화면에서 3D 모델을 회전·확대하여 볼 수 있습니다.",
        loadingHint: "모델을 불러오는 중…",
        previewAlt: "마쓰야마 공항 디오라마",
        viewerAlt: "마쓰야마 공항 디오라마",
      },
      contact: {
        title: "문의는 여기로",
        lead: "질문·협력 제안 등은 아래 이메일로 연락해 주세요.",
      },
      footer: {
        col1Title: "마쓰야마 미나미 고등학교",
        col1Body:
          "에히메현 마쓰야마시에 있는 고등학교입니다. 자연과학부가 중심이 되어 마쓰야마 관광 지원 프로젝트에 임하고 있습니다.",
        col2Title: "프로젝트",
        col3Title: "링크",
        schoolHp: "학교 홈페이지",
        virtualSpace: "가상 공간",
        officialInstagram: "공식 Instagram",
        copyrightBefore: "© ",
        copyrightAfter: " 마쓰야마 미나미 고등학교 자연과학부. All rights reserved.",
      },
    },
  };

  /**
   * @param {LocaleId} locale
   * @param {string} path dot.separated
   * @returns {string}
   */
  function getString(locale, path) {
    const parts = path.split(".").filter(Boolean);
    let cur = /** @type {unknown} */ (TRANSLATIONS[locale]);
    for (const p of parts) {
      if (cur == null || typeof cur !== "object") return "";
      cur = /** @type {Record<string, unknown>} */ (cur)[p];
    }
    return typeof cur === "string" ? cur : "";
  }

  /**
   * @returns {LocaleId}
   */
  function resolveLocale() {
    try {
      const stored = String(window.localStorage.getItem("site-locale") || "");
      if (stored === "ja" || stored === "zh" || stored === "zh-TW" || stored === "en" || stored === "ko") {
        return /** @type {LocaleId} */ (stored);
      }
    } catch (_) {
      /* プライベートモード等 */
    }
    const list =
      navigator.languages && navigator.languages.length > 0
        ? Array.from(navigator.languages)
        : [navigator.language || "ja"];
    for (const raw of list) {
      const tag = String(raw).toLowerCase().replace(/_/g, "-");
      if (tag === "zh-tw" || tag === "zh-hk" || tag.startsWith("zh-tw-") || tag.startsWith("zh-hk-")) {
        return "zh-TW";
      }
      const base = tag.split("-")[0];
      if (base === "ko") return "ko";
      if (base === "zh") return "zh";
      if (base === "en") return "en";
      if (base === "ja") return "ja";
    }
    return "en";
  }

  /**
   * @param {LocaleId} locale
   * @returns {string}
   */
  function documentLangFor(locale) {
    if (locale === "zh") return "zh-Hans";
    if (locale === "zh-TW") return "zh-TW";
    if (locale === "ko") return "ko";
    if (locale === "en") return "en";
    return "ja";
  }

  /**
   * @param {string} path
   * @param {Record<string, string|number>|undefined} vars
   */
  function t(path, vars) {
    let s = getString(activeLocale, path);
    if (!s) s = getString("ja", path);
    if (vars && typeof s === "string") {
      Object.keys(vars).forEach((k) => {
        s = s.split(`{${k}}`).join(String(vars[k]));
      });
    }
    return s;
  }

  /**
   * @param {LocaleId} locale
   */
  function applyLocale(locale) {
    if (!TRANSLATIONS[locale]) locale = "ja";
    activeLocale = locale;
    void ensureLocaleFonts(locale);
    document.documentElement.setAttribute("lang", documentLangFor(locale));
    const title = getString(locale, "meta.title") || getString("ja", "meta.title");
    if (title) document.title = title;

    document.querySelectorAll("[data-i18n]").forEach((el) => {
      const path = el.getAttribute("data-i18n");
      if (!path) return;
      const attr = el.getAttribute("data-i18n-attr");
      const text = getString(locale, path) || getString("ja", path);
      if (attr) {
        el.setAttribute(attr, text);
        return;
      }
      if (el.hasAttribute("data-i18n-html")) {
        el.innerHTML = text;
      } else {
        el.textContent = text;
      }
    });

    document.dispatchEvent(
      new CustomEvent("localechange", {
        detail: { locale },
      })
    );
  }

  window.I18n = {
    resolveLocale,
    applyLocale,
    t,
    getLocale: () => activeLocale,
    /** @param {LocaleId} loc */
    getString: (loc, path) => getString(loc, path),
  };

  function bootstrapLocale() {
    applyLocale(resolveLocale());
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bootstrapLocale);
  } else {
    bootstrapLocale();
  }
})();
