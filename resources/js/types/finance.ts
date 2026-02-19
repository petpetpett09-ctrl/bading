/**
 * Finance Module TypeScript Interfaces
 * Defines the structure of financial data throughout the application
 */

export interface Account {
  id: number;
  code: string;
  name: string;
  type: 'Asset' | 'Liability' | 'Equity' | 'Revenue' | 'Expense';
  category: string;
  balance: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Transaction {
  id: number;
  account_id: number;
  account?: Account;
  account_name?: string;
  date: string;
  description: string;
  amount: number;
  type: 'income' | 'expense' | 'transfer';
  category: string;
  reference_number?: string;
  notes?: string;
  created_at: string;
  updated_at: string;
}

export interface Invoice {
  id: number;
  invoice_number: string;
  customer: string;
  issue_date: string;
  due_date: string;
  total_amount: number;
  status: 'draft' | 'sent' | 'paid' | 'overdue';
  notes?: string;
  days_until_due?: number;
  created_at: string;
  updated_at: string;
}

export interface Bill {
  id: number;
  bill_number: string;
  vendor: string;
  bill_date: string;
  due_date: string;
  total_amount: number;
  status: 'draft' | 'received' | 'approved' | 'paid' | 'overdue';
  notes?: string;
  days_until_due?: number;
  created_at: string;
  updated_at: string;
}

export interface DashboardMetrics {
  current_revenue: number;
  current_expenses: number;
  net_profit: number;
  cash_flow_30_days: number;
  outstanding_invoices_count: number;
  outstanding_invoices_amount: number;
  upcoming_bills_count: number;
  upcoming_bills_amount: number;
}

export interface DashboardData {
  metrics: DashboardMetrics;
  recent_transactions: Transaction[];
  revenue_trend: Array<{ date: string; amount: number }>;
  expense_trend: Array<{ date: string; amount: number }>;
}

export interface TransactionFilters {
  start_date?: string;
  end_date?: string;
  type?: string;
  category?: string;
  account_id?: number;
  search?: string;
}

export interface TransactionResponse {
  data: Transaction[];
  categories: string[];
  accounts: Account[];
}

export interface InvoiceBillResponse {
  invoices: Invoice[];
  bills: Bill[];
  invoice_statuses: string[];
  bill_statuses: string[];
}

export interface ChartDataPoint {
  label: string;
  value: number;
}

export interface ReportSummary {
  total_revenue?: number;
  total_expenses?: number;
  net_profit?: number;
  total_inflow?: number;
  total_outflow?: number;
  net_cash_flow?: number;
}

export interface CategoryData {
  category: string;
  amount: number;
  percentage?: number;
}

export interface DailyFlowData {
  date: string;
  inflow: number;
  outflow: number;
  net_flow: number;
}

export interface ReportData {
  type: 'income_statement' | 'balance_sheet' | 'cash_flow' | 'expense_by_category' | 'revenue_by_customer';
  period_start: string;
  period_end: string;
  data: Record<string, any>;
  summary?: ReportSummary;
  revenue_by_category?: CategoryData[];
  expenses_by_category?: CategoryData[];
  daily_flow?: DailyFlowData[];
  categories?: CategoryData[];
}

export interface FinanceUser {
  id: number;
  name: string;
  email: string;
  role: string;
}
