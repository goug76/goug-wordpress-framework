// Our modules / classes

import KeyboardShortcutsAdmin from './modules/KeyboardShortcutsAdmin';
import QuickDraft from './modules/QuickDraft';
import PanelToggle from './modules/PanelToggle';
import PanelSort from './modules/PanelSort';
import DashboardApi from './services/DashboardApi';

// Instantiate a new object using our modules/classes

const keyboardShortcutsAdmin = new KeyboardShortcutsAdmin();
const quickDraft    = new QuickDraft();
const panelToggle   = new PanelToggle();
const panelSort     = new PanelSort();
const dashboardApi  = new DashboardApi();
